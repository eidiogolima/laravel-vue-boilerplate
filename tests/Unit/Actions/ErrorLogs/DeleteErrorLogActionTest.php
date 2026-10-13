<?php

declare(strict_types=1);

use App\Actions\ErrorLogs\DeleteErrorLogAction;
use App\Models\ErrorLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

pest()->extend(TestCase::class)->use(RefreshDatabase::class);

it('deletes only the given error log', function () {
    $errorLog = ErrorLog::factory()->create();
    $occurrence = ErrorLog::factory()->sameErrorAs($errorLog)->create();

    app(DeleteErrorLogAction::class)->handle($errorLog);

    expect($errorLog->fresh())->toBeNull()
        ->and($occurrence->fresh())->not->toBeNull();
});
