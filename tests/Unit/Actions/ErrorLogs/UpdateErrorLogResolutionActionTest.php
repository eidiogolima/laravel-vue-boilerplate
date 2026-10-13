<?php

declare(strict_types=1);

use App\Actions\ErrorLogs\UpdateErrorLogResolutionAction;
use App\Models\ErrorLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

pest()->extend(TestCase::class)->use(RefreshDatabase::class);

it('resolves every occurrence of the same error', function () {
    $errorLog = ErrorLog::factory()->create();
    $occurrence = ErrorLog::factory()->sameErrorAs($errorLog)->create();
    $otherErrorLog = ErrorLog::factory()->create();

    app(UpdateErrorLogResolutionAction::class)->handle($errorLog, isResolved: true);

    expect($errorLog->fresh()->isResolved())->toBeTrue()
        ->and($occurrence->fresh()->isResolved())->toBeTrue()
        ->and($otherErrorLog->fresh()->isResolved())->toBeFalse();
});

it('reopens every occurrence of the same error', function () {
    $errorLog = ErrorLog::factory()->resolved()->create();
    $occurrence = ErrorLog::factory()->resolved()->sameErrorAs($errorLog)->create();

    app(UpdateErrorLogResolutionAction::class)->handle($errorLog, isResolved: false);

    expect($errorLog->fresh()->isResolved())->toBeFalse()
        ->and($occurrence->fresh()->isResolved())->toBeFalse();
});
