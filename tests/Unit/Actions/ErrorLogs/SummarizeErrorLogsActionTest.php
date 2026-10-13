<?php

declare(strict_types=1);

use App\Actions\ErrorLogs\SummarizeErrorLogsAction;
use App\Models\ErrorLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

pest()->extend(TestCase::class)->use(RefreshDatabase::class);

it('summarizes the error logs', function () {
    $repeatedErrorLog = ErrorLog::factory()->create();
    ErrorLog::factory()->sameErrorAs($repeatedErrorLog)->create();
    ErrorLog::factory()->create(['created_at' => now()->subDays(2)]);
    ErrorLog::factory()->resolved()->create();

    expect(app(SummarizeErrorLogsAction::class)->handle())->toBe([
        'last_24_hours' => 3,
        'open' => 3,
        'distinct_open' => 2,
        'resolved' => 1,
        'total' => 4,
    ]);
});
