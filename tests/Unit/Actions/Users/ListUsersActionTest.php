<?php

declare(strict_types=1);

use App\Actions\Users\ListUsersAction;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

pest()->extend(TestCase::class)->use(RefreshDatabase::class);

it('paginates users', function () {
    User::factory()->count(5)->create();

    $users = app(ListUsersAction::class)->handle(perPage: 2);

    expect($users->total())->toBe(5)
        ->and($users->items())->toHaveCount(2)
        ->and($users->lastPage())->toBe(3);
});

it('filters users by name or email', function () {
    User::factory()->create(['name' => 'Maria Silva', 'email' => 'maria@example.com']);
    User::factory()->create(['name' => 'João Souza', 'email' => 'joao@company.com']);

    $listUsers = app(ListUsersAction::class);

    expect($listUsers->handle(search: 'Maria')->pluck('email')->all())->toBe(['maria@example.com'])
        ->and($listUsers->handle(search: 'company')->pluck('name')->all())->toBe(['João Souza']);
});

it('filters users by role', function () {
    $admin = User::factory()->admin()->create();
    User::factory()->count(2)->create();

    $users = app(ListUsersAction::class)->handle(role: UserRole::Admin);

    expect($users->pluck('id')->all())->toBe([$admin->id]);
});

it('lists the most recent users first', function () {
    $olderUser = User::factory()->create(['created_at' => now()->subDay()]);
    $newerUser = User::factory()->create();

    $users = app(ListUsersAction::class)->handle();

    expect($users->pluck('id')->all())->toBe([$newerUser->id, $olderUser->id]);
});
