<?php

declare(strict_types=1);

use Inertia\Testing\AssertableInertia as Assert;

test('pages receive the application locale', function () {
    $response = $this->get(route('login'));

    $response->assertInertia(fn (Assert $page) => $page->where('locale', 'pt_BR'));
});
