<?php

declare(strict_types=1);

use Laravel\Fortify\Features;

beforeEach(function () {
    $this->skipUnlessFortifyHas(Features::registration());
});

test('registration screen can be rendered', function () {
    $response = $this->get(route('register'));

    $response->assertOk();
});

test('new users can register', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('registration attempts are rate limited per ip address', function () {
    foreach (range(1, 20) as $attempt) {
        $this->post(route('register.store'), []);
    }

    $this->post(route('register.store'), [])->assertTooManyRequests();
});
