<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia as Assert;

test('guests are redirected to the login page', function (string $method, string $routeName) {
    $user = User::factory()->create();

    $this->{$method}(route($routeName, ['user' => $user]))
        ->assertRedirect(route('login'));
})->with([
    ['get', 'admin.users.index'],
    ['get', 'admin.users.create'],
    ['post', 'admin.users.store'],
    ['get', 'admin.users.edit'],
    ['put', 'admin.users.update'],
    ['delete', 'admin.users.destroy'],
]);

test('regular users cannot access the admin panel', function (string $method, string $routeName) {
    $regularUser = User::factory()->create();
    $managedUser = User::factory()->create();

    $this->actingAs($regularUser)
        ->{$method}(route($routeName, ['user' => $managedUser]))
        ->assertForbidden();

    expect($managedUser->fresh())->not->toBeNull();
})->with([
    ['get', 'admin.dashboard'],
    ['get', 'admin.users.index'],
    ['get', 'admin.users.create'],
    ['post', 'admin.users.store'],
    ['get', 'admin.users.edit'],
    ['put', 'admin.users.update'],
    ['delete', 'admin.users.destroy'],
]);

test('the admin dashboard redirects to user management', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->get(route('admin.dashboard'))
        ->assertRedirect('/admin/users');
});

test('admins can list users with pagination', function () {
    $admin = User::factory()->admin()->create();
    User::factory()->count(20)->create();

    $this->actingAs($admin)
        ->get(route('admin.users.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/users/Index')
            ->has('users.data', 15)
            ->where('users.meta.total', 21)
            ->where('users.meta.last_page', 2)
            ->where('roles', ['admin', 'user'])
            ->has('users.data.0', fn (Assert $user) => $user
                ->hasAll(['id', 'name', 'email', 'avatar', 'role', 'email_verified_at', 'created_at', 'can_be_deleted'])
                ->missing('password')
            ));

    $this->actingAs($admin)
        ->get(route('admin.users.index', ['page' => 2]))
        ->assertInertia(fn (Assert $page) => $page->has('users.data', 6));
});

test('admins can search users by name or email', function () {
    $admin = User::factory()->admin()->create(['name' => 'Admin', 'email' => 'admin@example.com']);
    User::factory()->create(['name' => 'Maria Silva', 'email' => 'maria@example.com']);
    User::factory()->create(['name' => 'João Souza', 'email' => 'joao@other.com']);

    $this->actingAs($admin)
        ->get(route('admin.users.index', ['search' => 'maria']))
        ->assertInertia(fn (Assert $page) => $page
            ->has('users.data', 1)
            ->where('users.data.0.email', 'maria@example.com')
            ->where('filters.search', 'maria'));

    $this->actingAs($admin)
        ->get(route('admin.users.index', ['search' => 'other.com']))
        ->assertInertia(fn (Assert $page) => $page
            ->has('users.data', 1)
            ->where('users.data.0.name', 'João Souza'));
});

test('admins can filter users by role', function () {
    $admin = User::factory()->admin()->create();
    User::factory()->count(3)->create();

    $this->actingAs($admin)
        ->get(route('admin.users.index', ['role' => 'admin']))
        ->assertInertia(fn (Assert $page) => $page
            ->has('users.data', 1)
            ->where('users.data.0.id', $admin->id)
            ->where('filters.role', 'admin'));
});

test('the role filter must be a valid role', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->get(route('admin.users.index', ['role' => 'superuser']))
        ->assertSessionHasErrors('role');
});

test('admins cannot delete themselves from the listing', function () {
    $admin = User::factory()->admin()->create();
    $otherUser = User::factory()->create();

    $this->actingAs($admin)
        ->get(route('admin.users.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->where('users.data', fn ($users) => collect($users)->firstWhere('id', $admin->id)['can_be_deleted'] === false
                && collect($users)->firstWhere('id', $otherUser->id)['can_be_deleted'] === true));
});

test('admins can view the create user page', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->get(route('admin.users.create'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/users/Create')
            ->where('roles', ['admin', 'user']));
});

test('admins can create users', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->post(route('admin.users.store'), [
            'name' => 'New Admin',
            'email' => 'new-admin@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'admin',
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.users.index'));

    $createdUser = User::query()->where('email', 'new-admin@example.com')->firstOrFail();

    expect($createdUser->name)->toBe('New Admin')
        ->and($createdUser->role)->toBe(UserRole::Admin);
});

test('creating a user validates the input', function (array $input, array $invalidFields) {
    User::factory()->create(['email' => 'taken@example.com']);

    $this->actingAs(User::factory()->admin()->create())
        ->post(route('admin.users.store'), [
            'name' => 'Valid Name',
            'email' => 'valid@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'user',
            ...$input,
        ])
        ->assertSessionHasErrors($invalidFields);

    expect(User::query()->where('email', 'valid@example.com')->exists())->toBeFalse();
})->with([
    'missing name' => [['name' => ''], ['name']],
    'invalid email' => [['email' => 'not-an-email'], ['email']],
    'duplicated email' => [['email' => 'taken@example.com'], ['email']],
    'missing password' => [['password' => '', 'password_confirmation' => ''], ['password']],
    'unconfirmed password' => [['password_confirmation' => 'different'], ['password']],
    'invalid role' => [['role' => 'superuser'], ['role']],
]);

test('admins can view the edit user page', function () {
    $user = User::factory()->create();

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('admin.users.edit', $user))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/users/Edit')
            ->where('user.id', $user->id)
            ->where('user.email', $user->email));
});

test('admins can update users', function () {
    $user = User::factory()->create();

    $this->actingAs(User::factory()->admin()->create())
        ->put(route('admin.users.update', $user), [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'role' => 'admin',
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.users.index'));

    $user->refresh();

    expect($user->name)->toBe('Updated Name')
        ->and($user->email)->toBe('updated@example.com')
        ->and($user->role)->toBe(UserRole::Admin)
        ->and($user->email_verified_at)->toBeNull();
});

test('admins can change a user password', function () {
    $user = User::factory()->create();

    $this->actingAs(User::factory()->admin()->create())
        ->put(route('admin.users.update', $user), [
            'name' => $user->name,
            'email' => $user->email,
            'role' => 'user',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])
        ->assertSessionHasNoErrors();

    expect(Hash::check('new-password', $user->refresh()->password))->toBeTrue();
});

test('updating a user validates the input', function (array $input, array $invalidFields) {
    $user = User::factory()->create();
    User::factory()->create(['email' => 'taken@example.com']);

    $this->actingAs(User::factory()->admin()->create())
        ->put(route('admin.users.update', $user), [
            'name' => 'Valid Name',
            'email' => $user->email,
            'role' => 'user',
            ...$input,
        ])
        ->assertSessionHasErrors($invalidFields);
})->with([
    'missing name' => [['name' => ''], ['name']],
    'duplicated email' => [['email' => 'taken@example.com'], ['email']],
    'unconfirmed password' => [['password' => 'new-password', 'password_confirmation' => 'different'], ['password']],
    'invalid role' => [['role' => 'superuser'], ['role']],
]);

test('admins cannot remove their own admin role', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->put(route('admin.users.update', $admin), [
            'name' => $admin->name,
            'email' => $admin->email,
            'role' => 'user',
        ])
        ->assertSessionHasErrors(['role' => __('errors.user.cannot_change_own_role')]);

    expect($admin->refresh()->role)->toBe(UserRole::Admin);
});

test('admins can delete users', function () {
    $user = User::factory()->create();

    $this->actingAs(User::factory()->admin()->create())
        ->delete(route('admin.users.destroy', $user))
        ->assertRedirect(route('admin.users.index'));

    expect($user->fresh())->toBeNull();
});

test('admins cannot delete themselves', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->delete(route('admin.users.destroy', $admin))
        ->assertForbidden();

    expect($admin->fresh())->not->toBeNull();
});

test('new users receive the user role by default', function () {
    $user = User::query()->create([
        'name' => 'Regular User',
        'email' => 'regular@example.com',
        'password' => 'password',
    ]);

    expect($user->refresh()->role)->toBe(UserRole::User)
        ->and($user->isAdmin())->toBeFalse();
});
