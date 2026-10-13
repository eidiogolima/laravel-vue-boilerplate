<?php

declare(strict_types=1);

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('pages receive the application locale', function () {
    $response = $this->get(route('login'));

    $response->assertInertia(fn (Assert $page) => $page->where('locale', 'pt_BR'));
});

test('admins receive the admin panel ability', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->get(route('dashboard'))
        ->assertInertia(fn (Assert $page) => $page->where('auth.can.accessAdminPanel', true));
});

test('regular users do not receive the admin panel ability', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('dashboard'))
        ->assertInertia(fn (Assert $page) => $page->where('auth.can.accessAdminPanel', false));
});
