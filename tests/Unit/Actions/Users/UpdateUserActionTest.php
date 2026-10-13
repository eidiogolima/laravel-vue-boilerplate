<?php

declare(strict_types=1);

use App\Actions\Users\UpdateUserAction;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

pest()->extend(TestCase::class)->use(RefreshDatabase::class);

it('updates the user details and role', function () {
    $user = User::factory()->create();

    app(UpdateUserAction::class)->handle($user, 'New Name', $user->email, UserRole::Admin);

    $user->refresh();

    expect($user->name)->toBe('New Name')
        ->and($user->role)->toBe(UserRole::Admin)
        ->and($user->email_verified_at)->not->toBeNull()
        ->and(Hash::check('password', $user->password))->toBeTrue();
});

it('resets the email verification when the email changes', function () {
    $user = User::factory()->create();

    app(UpdateUserAction::class)->handle($user, $user->name, 'changed@example.com', UserRole::User);

    expect($user->refresh()->email_verified_at)->toBeNull();
});

it('changes the password when a new one is given', function () {
    $user = User::factory()->create();

    app(UpdateUserAction::class)->handle($user, $user->name, $user->email, UserRole::User, 'new-password');

    expect(Hash::check('new-password', $user->refresh()->password))->toBeTrue();
});
