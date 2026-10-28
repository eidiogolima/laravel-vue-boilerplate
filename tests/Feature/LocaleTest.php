<?php

declare(strict_types=1);

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('the locale can be changed by guests', function () {
    $response = $this->withHeader('X-Inertia', 'true')
        ->from(route('login'))
        ->post(route('locale.update'), ['locale' => 'es']);

    $response->assertStatus(409)
        ->assertHeader('X-Inertia-Location', route('login'))
        ->assertCookie('locale', 'es');
});

test('the locale can be changed by authenticated users', function () {
    $response = $this->actingAs(User::factory()->create())
        ->withHeader('X-Inertia', 'true')
        ->from(route('dashboard'))
        ->post(route('locale.update'), ['locale' => 'en']);

    $response->assertStatus(409)
        ->assertHeader('X-Inertia-Location', route('dashboard'))
        ->assertCookie('locale', 'en');
});

test('the locale redirects to the previous page for regular requests', function () {
    $this->from(route('login'))
        ->post(route('locale.update'), ['locale' => 'es'])
        ->assertRedirect(route('login'))
        ->assertCookie('locale', 'es');
});

test('the locale redirects home when there is no previous page', function () {
    $this->withHeader('X-Inertia', 'true')
        ->post(route('locale.update'), ['locale' => 'pt_BR'])
        ->assertHeader('X-Inertia-Location', route('home'));
});

test('the locale is validated', function (mixed $locale) {
    $this->from(route('login'))
        ->post(route('locale.update'), ['locale' => $locale])
        ->assertSessionHasErrors('locale')
        ->assertCookieMissing('locale');
})->with([null, '', 'fr', [['es']]]);

test('pages use the locale stored in the cookie', function (string $locale) {
    $this->withCookie('locale', $locale)
        ->get(route('login'))
        ->assertInertia(fn (Assert $page) => $page->where('locale', $locale));

    expect(app()->getLocale())->toBe($locale);
})->with(['en', 'es', 'pt_BR']);

test('pages use the configured locale when the cookie is missing or invalid', function (?string $cookieLocale) {
    config()->set('app.locale', 'es');

    $request = $cookieLocale === null ? $this : $this->withCookie('locale', $cookieLocale);

    $request->get(route('login'))
        ->assertInertia(fn (Assert $page) => $page->where('locale', 'es'));
})->with([null, 'fr']);

test('pages receive the available locales', function () {
    $this->get(route('login'))->assertInertia(fn (Assert $page) => $page
        ->where('locales', [
            ['value' => 'en', 'label' => 'English'],
            ['value' => 'es', 'label' => 'Español'],
            ['value' => 'pt_BR', 'label' => 'Português (Brasil)'],
        ]));
});

test('server messages are translated to the selected locale', function (string $locale, string $expectedMessage) {
    $this->withCookie('locale', $locale);

    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('notifications.read-all'))
        ->assertSessionHas('inertia.flash_data.toast.message', $expectedMessage);
})->with([
    ['en', 'All notifications were marked as read.'],
    ['es', 'Todas las notificaciones fueron marcadas como leídas.'],
    ['pt_BR', 'Todas as notificações foram marcadas como lidas.'],
]);

test('the html document language follows the selected locale', function () {
    $this->withCookie('locale', 'es')
        ->get(route('home'))
        ->assertSee('<html lang="es"', false);
});
