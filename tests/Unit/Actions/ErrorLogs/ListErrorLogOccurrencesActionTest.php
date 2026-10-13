<?php

declare(strict_types=1);

use App\Actions\ErrorLogs\ListErrorLogOccurrencesAction;
use App\Models\ErrorLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

pest()->extend(TestCase::class)->use(RefreshDatabase::class);

it('paginates the other occurrences of the same error', function () {
    $errorLog = ErrorLog::factory()->create();
    ErrorLog::factory()->count(3)->sameErrorAs($errorLog)->create();
    ErrorLog::factory()->create();

    $occurrences = app(ListErrorLogOccurrencesAction::class)->handle($errorLog, perPage: 2);

    expect($occurrences->total())->toBe(3)
        ->and($occurrences->items())->toHaveCount(2)
        ->and($occurrences->getPageName())->toBe(ListErrorLogOccurrencesAction::PAGE_NAME)
        ->and($occurrences->pluck('id')->all())->not->toContain($errorLog->id);
});

it('lists the most recent occurrences first', function () {
    $errorLog = ErrorLog::factory()->create();
    $olderOccurrence = ErrorLog::factory()->sameErrorAs($errorLog)->create();
    $newerOccurrence = ErrorLog::factory()->sameErrorAs($errorLog)->create();

    expect(app(ListErrorLogOccurrencesAction::class)->handle($errorLog)->pluck('id')->all())
        ->toBe([$newerOccurrence->id, $olderOccurrence->id]);
});
