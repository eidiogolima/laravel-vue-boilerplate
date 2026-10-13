<?php

declare(strict_types=1);

use App\Actions\ErrorLogs\SummarizeErrorLogOccurrencesAction;
use App\Models\ErrorLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

pest()->extend(TestCase::class)->use(RefreshDatabase::class);

it('summarizes how often the same error happened', function () {
    $firstOccurredAt = Carbon::parse('2026-10-01 08:00:00');
    $lastOccurredAt = Carbon::parse('2026-10-10 18:30:00');

    $errorLog = ErrorLog::factory()->create(['created_at' => $firstOccurredAt]);
    ErrorLog::factory()->sameErrorAs($errorLog)->create(['created_at' => $lastOccurredAt]);
    ErrorLog::factory()->create(['created_at' => now()]);

    expect(app(SummarizeErrorLogOccurrencesAction::class)->handle($errorLog))->toBe([
        'total' => 2,
        'first_occurred_at' => $firstOccurredAt->toIso8601String(),
        'last_occurred_at' => $lastOccurredAt->toIso8601String(),
    ]);
});
