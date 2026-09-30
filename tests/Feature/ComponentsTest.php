<?php

declare(strict_types=1);

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('components'));

    $response->assertRedirect(route('login'));
});

test('unverified users are redirected to the email verification prompt', function () {
    $user = User::factory()->unverified()->create();

    $response = $this->actingAs($user)->get(route('components'));

    $response->assertRedirect(route('verification.notice'));
});

test('verified users can visit the components page', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('components'));

    $response->assertInertia(fn (Assert $page) => $page->component('Components'));
});
