<?php

declare(strict_types=1);

test('responses carry the baseline security headers', function () {
    $response = $this->get(route('home'));

    $response->assertHeader('X-Content-Type-Options', 'nosniff')
        ->assertHeader('X-Frame-Options', 'DENY')
        ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
        ->assertHeader('Cross-Origin-Opener-Policy', 'same-origin')
        ->assertHeaderMissing('Strict-Transport-Security');
});

test('secure requests receive the strict transport security header', function () {
    $this->get('https://localhost/')
        ->assertHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
});

test('the content security policy is enforced with a nonce outside local environments', function () {
    $this->app['env'] = 'production';

    $response = $this->get(route('home'));

    $policy = $response->headers->get('Content-Security-Policy');

    expect($policy)
        ->toContain("default-src 'self'")
        ->toContain("object-src 'none'")
        ->toContain("frame-ancestors 'none'")
        ->toMatch("/script-src 'self' 'nonce-[A-Za-z0-9+\/=]+'/");

    preg_match("/'nonce-([^']+)'/", (string) $policy, $matches);

    expect($response->getContent())->toContain('nonce="'.$matches[1].'"');
});

test('the content security policy is skipped in the local environment', function () {
    $this->app['env'] = 'local';

    $this->get(route('home'))->assertHeaderMissing('Content-Security-Policy');
});
