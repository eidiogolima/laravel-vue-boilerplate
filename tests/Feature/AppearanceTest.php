<?php

declare(strict_types=1);

use App\Http\Middleware\HandleAppearance;
use App\Models\User;

test('the stored appearance applies to the authenticated area', function () {
    $this->actingAs(User::factory()->create())
        ->withUnencryptedCookie('appearance', 'dark')
        ->get(route('dashboard'))
        ->assertSee('class="dark"', false)
        ->assertSee('data-public-page="false"', false);
});

test('the stored appearance does not apply to public pages', function (string $routeName) {
    $this->withUnencryptedCookie('appearance', 'dark')
        ->get(route($routeName))
        ->assertDontSee('class="dark"', false)
        ->assertSee('data-public-page="true"', false);
})->with(['home', 'login', 'register']);

test('the stored appearance does not apply to the home page of authenticated users', function () {
    $this->actingAs(User::factory()->create())
        ->withUnencryptedCookie('appearance', 'dark')
        ->get(route('home'))
        ->assertDontSee('class="dark"', false);
});

test('only the welcome, error and authentication pages are public', function (string $component, bool $isPublic) {
    expect(HandleAppearance::isPublicPage($component))->toBe($isPublic);
})->with([
    ['Welcome', true],
    ['ErrorPage', true],
    ['auth/Login', true],
    ['Dashboard', false],
    ['settings/Appearance', false],
    ['admin/users/Index', false],
]);

test('public pages follow the system appearance', function () {
    expect(HandleAppearance::appearanceFor('Welcome', 'dark'))->toBe('system')
        ->and(HandleAppearance::appearanceFor('Dashboard', 'dark'))->toBe('dark');
});
