<?php

declare(strict_types=1);

use App\Actions\ErrorLogs\RecordErrorLogAction;
use App\Enums\ErrorLogContext;
use App\Models\ErrorLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

pest()->extend(TestCase::class)->use(RefreshDatabase::class);

it('records the exception details', function () {
    $exception = new RuntimeException('Something failed');

    app(RecordErrorLogAction::class)->handle($exception);

    $errorLog = ErrorLog::query()->sole();

    expect($errorLog->exception)->toBe(RuntimeException::class)
        ->and($errorLog->message)->toBe('Something failed')
        ->and($errorLog->status_code)->toBe(500)
        ->and($errorLog->file)->toBe('tests/Unit/Actions/ErrorLogs/RecordErrorLogActionTest.php')
        ->and($errorLog->line)->toBe($exception->getLine())
        ->and($errorLog->hash)->toBe(sha1(RuntimeException::class.'|'.$exception->getFile().'|'.$exception->getLine()))
        ->and($errorLog->context)->toBe(ErrorLogContext::Http)
        ->and($errorLog->environment)->toBe('testing')
        ->and($errorLog->code_snippet)->not->toBeEmpty()
        ->and($errorLog->trace)->not->toBeEmpty()
        ->and($errorLog->isResolved())->toBeFalse();
});

it('uses the status code of http exceptions', function () {
    app(RecordErrorLogAction::class)->handle(new HttpException(503, 'Unavailable'));

    expect(ErrorLog::query()->sole()->status_code)->toBe(503);
});

it('records the authenticated user', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    app(RecordErrorLogAction::class)->handle(new RuntimeException('Failure'));

    expect(ErrorLog::query()->sole()->user_id)->toBe($user->id);
});

it('gives repeated errors the same hash', function () {
    $recordErrorLog = app(RecordErrorLogAction::class);

    foreach (range(1, 2) as $attempt) {
        $recordErrorLog->handle(new RuntimeException("Attempt {$attempt}"));
    }

    expect(ErrorLog::query()->distinct()->count('hash'))->toBe(1);
});

it('never throws when the error log cannot be stored', function () {
    Schema::drop('error_logs');

    app(RecordErrorLogAction::class)->handle(new RuntimeException('Failure'));

    expect(Schema::hasTable('error_logs'))->toBeFalse();
});
