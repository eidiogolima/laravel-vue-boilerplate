<?php

declare(strict_types=1);

use App\Actions\Users\CreateUserAction;
use App\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

pest()->extend(TestCase::class)->use(RefreshDatabase::class);

it('creates a user with the given role and a hashed password', function () {
    $user = app(CreateUserAction::class)->handle('Maria Silva', 'maria@example.com', 'secret-password', UserRole::Admin);

    expect($user->exists)->toBeTrue()
        ->and($user->name)->toBe('Maria Silva')
        ->and($user->email)->toBe('maria@example.com')
        ->and($user->role)->toBe(UserRole::Admin)
        ->and(Hash::check('secret-password', $user->password))->toBeTrue();
});
