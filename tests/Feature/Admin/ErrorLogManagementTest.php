<?php

declare(strict_types=1);

use App\Actions\ErrorLogs\RedactSensitiveDataAction;
use App\Enums\ErrorLogContext;
use App\Models\ErrorLog;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Inertia\Testing\AssertableInertia as Assert;

test('guests are redirected to the login page', function (string $method, string $routeName) {
    $errorLog = ErrorLog::factory()->create();

    $this->{$method}(route($routeName, ['errorLog' => $errorLog]))
        ->assertRedirect(route('login'));
})->with([
    ['get', 'admin.error-logs.index'],
    ['get', 'admin.error-logs.show'],
    ['patch', 'admin.error-logs.resolution'],
    ['delete', 'admin.error-logs.destroy'],
    ['get', 'admin.error-logs.test-error'],
]);

test('regular users cannot access error logs', function (string $method, string $routeName) {
    $errorLog = ErrorLog::factory()->create();
    $countBefore = ErrorLog::query()->count();

    $this->actingAs(User::factory()->create())
        ->{$method}(route($routeName, ['errorLog' => $errorLog]), ['resolved' => true])
        ->assertForbidden();

    expect($errorLog->fresh()->resolved_at)->toBeNull()
        ->and(ErrorLog::query()->count())->toBe($countBefore);
})->with([
    ['get', 'admin.error-logs.index'],
    ['get', 'admin.error-logs.show'],
    ['patch', 'admin.error-logs.resolution'],
    ['delete', 'admin.error-logs.destroy'],
    ['get', 'admin.error-logs.test-error'],
]);

test('regular users do not receive the open error logs count', function () {
    ErrorLog::factory()->create();

    $this->actingAs(User::factory()->create())
        ->get(route('dashboard'))
        ->assertInertia(fn (Assert $page) => $page->where('openErrorLogsCount', 0));
});

test('admins receive the open error logs count', function () {
    ErrorLog::factory()->count(2)->create();
    ErrorLog::factory()->resolved()->create();

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('dashboard'))
        ->assertInertia(fn (Assert $page) => $page->where('openErrorLogsCount', 2));
});

test('admins can list open error logs with pagination and a summary', function () {
    ErrorLog::factory()->count(25)->create();
    ErrorLog::factory()->resolved()->create();

    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.error-logs.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/error-logs/Index')
            ->has('errorLogs.data', 20)
            ->where('errorLogs.meta.total', 25)
            ->where('errorLogs.meta.last_page', 2)
            ->where('summary.open', 25)
            ->where('summary.resolved', 1)
            ->where('summary.total', 26)
            ->where('filters', ['search' => '', 'resolution' => 'open', 'status' => '', 'sort' => 'newest'])
            ->has('errorLogs.data.0', fn (Assert $errorLog) => $errorLog
                ->hasAll(['id', 'status_code', 'exception', 'message', 'file', 'line', 'method', 'url', 'context', 'user_name', 'is_resolved', 'created_at'])
                ->missing('payload')
                ->missing('headers')
            ));

    $this->actingAs($admin)
        ->get(route('admin.error-logs.index', ['page' => 2]))
        ->assertInertia(fn (Assert $page) => $page->has('errorLogs.data', 5));
});

test('admins can filter error logs', function (array $query, string $expectedMessage) {
    ErrorLog::factory()->create(['message' => 'Open server error']);
    ErrorLog::factory()->withStatusCode(404)->create(['message' => 'Open client error']);
    ErrorLog::factory()->resolved()->create(['message' => 'Resolved server error']);

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('admin.error-logs.index', $query))
        ->assertInertia(fn (Assert $page) => $page
            ->has('errorLogs.data', 1)
            ->where('errorLogs.data.0.message', $expectedMessage));
})->with([
    'resolved' => [['resolution' => 'resolved'], 'Resolved server error'],
    'client errors' => [['status' => '4xx'], 'Open client error'],
    'server errors' => [['status' => '5xx'], 'Open server error'],
    'search' => [['search' => 'client', 'resolution' => 'all'], 'Open client error'],
]);

test('admins can sort error logs from oldest to newest', function () {
    $oldestErrorLog = ErrorLog::factory()->create();
    ErrorLog::factory()->create();

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('admin.error-logs.index', ['sort' => 'oldest']))
        ->assertInertia(fn (Assert $page) => $page
            ->where('errorLogs.data.0.id', $oldestErrorLog->id)
            ->where('filters.sort', 'oldest'));
});

test('the error log filters are validated', function (array $query, string $invalidField) {
    $this->actingAs(User::factory()->admin()->create())
        ->get(route('admin.error-logs.index', $query))
        ->assertSessionHasErrors($invalidField);
})->with([
    'invalid resolution' => [['resolution' => 'archived'], 'resolution'],
    'invalid status' => [['status' => '3xx'], 'status'],
    'invalid sort' => [['sort' => 'random'], 'sort'],
    'search too long' => [['search' => str_repeat('a', 256)], 'search'],
]);

test('admins can view an error log with its paginated occurrences', function () {
    $user = User::factory()->create();
    $errorLog = ErrorLog::factory()->create(['user_id' => $user->id]);
    ErrorLog::factory()->count(12)->sameErrorAs($errorLog)->create();
    ErrorLog::factory()->create();

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('admin.error-logs.show', $errorLog))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/error-logs/Show')
            ->where('errorLog.id', $errorLog->id)
            ->where('errorLog.exception', RuntimeException::class)
            ->where('errorLog.user.email', $user->email)
            ->has('errorLog.trace')
            ->has('errorLog.code_snippet')
            ->has('occurrences.data', 10)
            ->where('occurrences.meta.total', 12)
            ->where('occurrenceSummary.total', 13));
});

test('a missing error log returns not found', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->get(route('admin.error-logs.show', 999))
        ->assertNotFound();
});

test('resolving an error log resolves every occurrence of the same error', function () {
    $errorLog = ErrorLog::factory()->create();
    $occurrence = ErrorLog::factory()->sameErrorAs($errorLog)->create();
    $otherErrorLog = ErrorLog::factory()->create();

    $this->actingAs(User::factory()->admin()->create())
        ->from(route('admin.error-logs.show', $errorLog))
        ->patch(route('admin.error-logs.resolution', $errorLog), ['resolved' => true])
        ->assertRedirect(route('admin.error-logs.show', $errorLog))
        ->assertInertiaFlash('toast', ['type' => 'success', 'message' => __('messages.error_log.resolved')]);

    expect($errorLog->fresh()->isResolved())->toBeTrue()
        ->and($occurrence->fresh()->isResolved())->toBeTrue()
        ->and($otherErrorLog->fresh()->isResolved())->toBeFalse();
});

test('admins can reopen a resolved error log', function () {
    $errorLog = ErrorLog::factory()->resolved()->create();

    $this->actingAs(User::factory()->admin()->create())
        ->patch(route('admin.error-logs.resolution', $errorLog), ['resolved' => false])
        ->assertInertiaFlash('toast', ['type' => 'success', 'message' => __('messages.error_log.reopened')]);

    expect($errorLog->fresh()->isResolved())->toBeFalse();
});

test('the resolution status is validated', function (array $input) {
    $errorLog = ErrorLog::factory()->create();

    $this->actingAs(User::factory()->admin()->create())
        ->patch(route('admin.error-logs.resolution', $errorLog), $input)
        ->assertSessionHasErrors('resolved');
})->with([
    'missing' => [[]],
    'not a boolean' => [['resolved' => 'maybe']],
]);

test('admins can delete an error log', function () {
    $errorLog = ErrorLog::factory()->create();

    $this->actingAs(User::factory()->admin()->create())
        ->delete(route('admin.error-logs.destroy', $errorLog))
        ->assertRedirect(route('admin.error-logs.index'))
        ->assertInertiaFlash('toast', ['type' => 'success', 'message' => __('messages.error_log.deleted')]);

    expect($errorLog->fresh())->toBeNull();
});

test('the test error page throws and the error is recorded', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->get(route('admin.error-logs.test-error'))
        ->assertInternalServerError();

    expect(ErrorLog::query()->sole()->route)->toBe('admin.error-logs.test-error');
});

test('unhandled exceptions are recorded with request data and sensitive fields redacted', function () {
    Route::middleware('web')->post('/testing/error-logs', fn () => throw new LogicException('Something broke'));

    $user = User::factory()->create();

    $this->actingAs($user)
        ->post('/testing/error-logs', [
            'name' => 'Maria',
            'password' => 'secret-password',
            'card' => ['number' => '4111111111111111'],
        ], ['Authorization' => 'Bearer secret-token'])
        ->assertInternalServerError();

    $errorLog = ErrorLog::query()->sole();

    expect($errorLog->exception)->toBe(LogicException::class)
        ->and($errorLog->message)->toBe('Something broke')
        ->and($errorLog->status_code)->toBe(500)
        ->and($errorLog->context)->toBe(ErrorLogContext::Http)
        ->and($errorLog->method)->toBe('POST')
        ->and($errorLog->user_id)->toBe($user->id)
        ->and($errorLog->payload['name'])->toBe('Maria')
        ->and($errorLog->payload['password'])->toBe(RedactSensitiveDataAction::REDACTED)
        ->and($errorLog->payload['card'])->toBe(RedactSensitiveDataAction::REDACTED)
        ->and($errorLog->headers['authorization'])->toBe(RedactSensitiveDataAction::REDACTED)
        ->and($errorLog->code_snippet)->not->toBeEmpty()
        ->and($errorLog->trace)->not->toBeEmpty();
});

test('expected http errors are not recorded', function () {
    $this->get('/this-page-does-not-exist')->assertNotFound();

    expect(ErrorLog::query()->count())->toBe(0);
});
