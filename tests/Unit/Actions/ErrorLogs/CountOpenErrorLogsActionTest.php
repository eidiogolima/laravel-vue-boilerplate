<?php

declare(strict_types=1);

use App\Actions\ErrorLogs\CountOpenErrorLogsAction;
use App\Models\ErrorLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

pest()->extend(TestCase::class)->use(RefreshDatabase::class);

it('counts only unresolved error logs', function () {
    ErrorLog::factory()->count(3)->create();
    ErrorLog::factory()->resolved()->create();

    expect(app(CountOpenErrorLogsAction::class)->handle())->toBe(3);
});
