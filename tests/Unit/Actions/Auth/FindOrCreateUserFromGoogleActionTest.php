<?php

declare(strict_types=1);

use App\Actions\Auth\FindOrCreateUserFromGoogleAction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Two\User as GoogleUser;
use Tests\TestCase;

pest()->extend(TestCase::class)->use(RefreshDatabase::class);

it('creates a verified user from a new google account', function () {
    $googleUser = GoogleUser::fake([
        'id' => 'google-123',
        'name' => 'Ana Souza',
        'email' => 'ana@example.com',
        'avatar' => 'https://example.com/ana.jpg',
        'email_verified' => true,
    ]);

    $user = app(FindOrCreateUserFromGoogleAction::class)->handle($googleUser);

    expect($user)
        ->name->toBe('Ana Souza')
        ->email->toBe('ana@example.com')
        ->google_id->toBe('google-123')
        ->avatar->toBe('https://example.com/ana.jpg')
        ->email_verified_at->not->toBeNull();
});

it('links the google account to an existing user with the same email', function () {
    $existingUser = User::factory()->create(['email' => 'ana@example.com']);
    $googleUser = GoogleUser::fake([
        'id' => 'google-123',
        'email' => 'ana@example.com',
        'email_verified' => true,
    ]);

    $user = app(FindOrCreateUserFromGoogleAction::class)->handle($googleUser);

    expect($user->id)->toBe($existingUser->id)
        ->and($user->google_id)->toBe('google-123')
        ->and(User::query()->count())->toBe(1);
});

it('finds the user by google id even when the google email changed', function () {
    $existingUser = User::factory()->create([
        'email' => 'old@example.com',
        'google_id' => 'google-123',
    ]);
    $googleUser = GoogleUser::fake([
        'id' => 'google-123',
        'email' => 'new@example.com',
        'email_verified' => true,
    ]);

    $user = app(FindOrCreateUserFromGoogleAction::class)->handle($googleUser);

    expect($user->id)->toBe($existingUser->id)
        ->and($user->email)->toBe('old@example.com');
});

it('marks an unverified existing user as verified', function () {
    User::factory()->unverified()->create(['email' => 'ana@example.com']);
    $googleUser = GoogleUser::fake([
        'email' => 'ana@example.com',
        'email_verified' => true,
    ]);

    $user = app(FindOrCreateUserFromGoogleAction::class)->handle($googleUser);

    expect($user->email_verified_at)->not->toBeNull();
});

it('returns null when google does not confirm the email', function (array $googleAttributes) {
    $googleUser = GoogleUser::fake($googleAttributes);

    $user = app(FindOrCreateUserFromGoogleAction::class)->handle($googleUser);

    expect($user)->toBeNull()
        ->and(User::query()->count())->toBe(0);
})->with([
    'unverified email' => [['email' => 'ana@example.com', 'email_verified' => false]],
    'missing verification flag' => [['email' => 'ana@example.com']],
    'missing email' => [['email' => null, 'email_verified' => true]],
]);
