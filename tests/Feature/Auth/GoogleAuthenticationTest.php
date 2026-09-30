<?php

declare(strict_types=1);

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;
use Laravel\Socialite\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Laravel\Socialite\Two\User as GoogleUser;

beforeEach(function () {
    config([
        'services.google.client_id' => 'google-client-id',
        'services.google.client_secret' => 'google-client-secret',
    ]);
});

test('guests are redirected to google', function () {
    Socialite::fake('google');

    $response = $this->get(route('auth.google.redirect'));

    $response->assertRedirect();
});

test('authenticated users cannot start a google login', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('auth.google.redirect'));

    $response->assertRedirect(route('dashboard'));
});

test('a new user is created and logged in from a verified google account', function () {
    Socialite::fake('google', GoogleUser::fake([
        'id' => 'google-123',
        'name' => 'Ana Souza',
        'email' => 'ana@example.com',
        'email_verified' => true,
    ]));

    $response = $this->get(route('auth.google.callback'));

    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticated();
    $this->assertDatabaseHas('users', [
        'email' => 'ana@example.com',
        'google_id' => 'google-123',
    ]);
});

test('users with two factor authentication are sent to the two factor challenge', function () {
    $user = User::factory()->withTwoFactor()->create(['google_id' => 'google-123']);
    Socialite::fake('google', GoogleUser::fake([
        'id' => 'google-123',
        'email' => $user->email,
        'email_verified' => true,
    ]));

    $response = $this->get(route('auth.google.callback'));

    $response->assertRedirect(route('two-factor.login'))
        ->assertSessionHas('login.id', $user->id);
    $this->assertGuest();
});

test('google accounts with an unverified email are rejected', function () {
    Socialite::fake('google', GoogleUser::fake([
        'email' => 'ana@example.com',
        'email_verified' => false,
    ]));

    $response = $this->get(route('auth.google.callback'));

    $response->assertRedirect(route('login'))
        ->assertSessionHasErrors(['email' => 'Seu e-mail do Google não está verificado. Verifique-o no Google e tente novamente.']);
    $this->assertGuest();
    $this->assertDatabaseMissing('users', ['email' => 'ana@example.com']);
});

test('a cancelled google login returns to the login page with an error', function () {
    $response = $this->get(route('auth.google.callback', ['error' => 'access_denied']));

    $response->assertRedirect(route('login'))
        ->assertSessionHasErrors(['email' => 'O login com o Google foi cancelado.']);
    $this->assertGuest();
});

test('an invalid google callback returns to the login page with an error', function () {
    Socialite::fake('google', fn () => throw new InvalidStateException);

    $response = $this->get(route('auth.google.callback'));

    $response->assertRedirect(route('login'))
        ->assertSessionHasErrors(['email' => 'Não foi possível entrar com o Google. Tente novamente.']);
    $this->assertGuest();
});

test('the login page offers google login only when it is configured', function (?string $clientId, bool $canLogInWithGoogle) {
    config(['services.google.client_id' => $clientId]);

    $response = $this->get(route('login'));

    $response->assertInertia(fn (Assert $page) => $page->where('canLogInWithGoogle', $canLogInWithGoogle));
})->with([
    'configured' => ['google-client-id', true],
    'not configured' => [null, false],
]);
