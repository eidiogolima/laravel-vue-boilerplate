<?php

declare(strict_types=1);

use App\Actions\ErrorLogs\ListErrorLogsAction;
use App\Enums\ErrorLogResolution;
use App\Enums\HttpStatusCodeRange;
use App\Enums\SortOrder;
use App\Models\ErrorLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

pest()->extend(TestCase::class)->use(RefreshDatabase::class);

it('paginates open error logs by default', function () {
    ErrorLog::factory()->count(5)->create();
    ErrorLog::factory()->resolved()->create();

    $errorLogs = app(ListErrorLogsAction::class)->handle(perPage: 2);

    expect($errorLogs->total())->toBe(5)
        ->and($errorLogs->items())->toHaveCount(2)
        ->and($errorLogs->lastPage())->toBe(3);
});

it('filters error logs by resolution', function (ErrorLogResolution $resolution, int $expectedTotal) {
    ErrorLog::factory()->count(2)->create();
    ErrorLog::factory()->resolved()->create();

    expect(app(ListErrorLogsAction::class)->handle(resolution: $resolution)->total())->toBe($expectedTotal);
})->with([
    'open' => [ErrorLogResolution::Open, 2],
    'resolved' => [ErrorLogResolution::Resolved, 1],
    'all' => [ErrorLogResolution::All, 3],
]);

it('filters error logs by status code range', function () {
    $serverError = ErrorLog::factory()->withStatusCode(503)->create();
    $clientError = ErrorLog::factory()->withStatusCode(419)->create();

    $listErrorLogs = app(ListErrorLogsAction::class);

    expect($listErrorLogs->handle(statusCodeRange: HttpStatusCodeRange::ServerError)->pluck('id')->all())->toBe([$serverError->id])
        ->and($listErrorLogs->handle(statusCodeRange: HttpStatusCodeRange::ClientError)->pluck('id')->all())->toBe([$clientError->id]);
});

it('searches by message, exception, url or file', function (array $attributes, string $search) {
    $matchingErrorLog = ErrorLog::factory()->create($attributes);
    ErrorLog::factory()->create(['message' => 'Unrelated', 'exception' => LogicException::class, 'url' => 'https://example.com/other', 'file' => 'app/Other.php']);

    expect(app(ListErrorLogsAction::class)->handle(search: $search)->pluck('id')->all())->toBe([$matchingErrorLog->id]);
})->with([
    'message' => [['message' => 'Payment gateway timeout'], 'gateway'],
    'exception' => [['exception' => 'App\Exceptions\InvoiceException'], 'InvoiceException'],
    'url' => [['url' => 'https://example.com/invoices/42'], 'invoices'],
    'file' => [['file' => 'app/Actions/Billing/ChargeAction.php'], 'Billing'],
]);

it('sorts error logs by newest or oldest', function () {
    $olderErrorLog = ErrorLog::factory()->create();
    $newerErrorLog = ErrorLog::factory()->create();

    $listErrorLogs = app(ListErrorLogsAction::class);

    expect($listErrorLogs->handle()->pluck('id')->all())->toBe([$newerErrorLog->id, $olderErrorLog->id])
        ->and($listErrorLogs->handle(sortOrder: SortOrder::Oldest)->pluck('id')->all())->toBe([$olderErrorLog->id, $newerErrorLog->id]);
});
