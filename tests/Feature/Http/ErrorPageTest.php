<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Route;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    Route::middleware('web')->group(function () {
        Route::get('/testing/errors/{status}', fn (int $status) => abort($status));
        Route::get('/testing/errors-exception', fn () => throw new RuntimeException('Unexpected failure'));
        Route::post('/testing/errors-expired', fn () => throw new TokenMismatchException);
    });
});

test('missing pages render the error page', function () {
    $this->get('/this-page-does-not-exist')
        ->assertNotFound()
        ->assertInertia(fn (Assert $page) => $page
            ->component('ErrorPage')
            ->where('status', 404)
            ->where('locale', 'pt_BR')
        );
});

test('http errors render the error page with their status', function (int $status) {
    $this->actingAs(User::factory()->create())
        ->get("/testing/errors/{$status}")
        ->assertStatus($status)
        ->assertInertia(fn (Assert $page) => $page
            ->component('ErrorPage')
            ->where('status', $status)
            ->has('auth.user')
        );
})->with([403, 404, 429, 503]);

test('server errors render the error page when debug mode is disabled', function () {
    config(['app.debug' => false]);

    $this->get('/testing/errors-exception')
        ->assertInternalServerError()
        ->assertInertia(fn (Assert $page) => $page
            ->component('ErrorPage')
            ->where('status', 500)
        );
});

test('server errors keep the debug page when debug mode is enabled', function () {
    config(['app.debug' => true]);

    $this->get('/testing/errors-exception')
        ->assertInternalServerError()
        ->assertSee('Unexpected failure')
        ->assertSee('RuntimeException');
});

test('expired pages redirect back with an error toast', function () {
    $this->from('/dashboard')
        ->post('/testing/errors-expired')
        ->assertRedirect('/dashboard')
        ->assertInertiaFlash('toast', ['type' => 'error', 'message' => __('errors.http.page_expired')]);
});
