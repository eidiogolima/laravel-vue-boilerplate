<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    Route::get('/_test/request-info', fn (Request $request) => response()->json([
        'ip' => $request->ip(),
        'secure' => $request->secure(),
        'url' => $request->url(),
    ]));
});

test('requests forwarded by cloudflare through a private proxy resolve the client ip and scheme', function () {
    $response = $this->withServerVariables(['REMOTE_ADDR' => '172.18.0.2'])
        ->withHeaders([
            'X-Forwarded-For' => '203.0.113.10, 162.158.1.1',
            'X-Forwarded-Proto' => 'https',
            'X-Forwarded-Host' => 'app.example.com',
            'X-Forwarded-Port' => '443',
        ])
        ->get('/_test/request-info');

    $response->assertOk()->assertExactJson([
        'ip' => '203.0.113.10',
        'secure' => true,
        'url' => 'https://app.example.com/_test/request-info',
    ]);
});

test('requests coming directly from cloudflare resolve the client ip and scheme', function () {
    $response = $this->withServerVariables(['REMOTE_ADDR' => '104.16.0.1'])
        ->withHeaders([
            'X-Forwarded-For' => '203.0.113.10',
            'X-Forwarded-Proto' => 'https',
        ])
        ->get('/_test/request-info');

    $response->assertOk()
        ->assertJsonPath('ip', '203.0.113.10')
        ->assertJsonPath('secure', true);
});

test('forwarded headers from untrusted sources are ignored', function () {
    $response = $this->withServerVariables(['REMOTE_ADDR' => '8.8.8.8'])
        ->withHeaders([
            'X-Forwarded-For' => '203.0.113.10',
            'X-Forwarded-Proto' => 'https',
        ])
        ->get('/_test/request-info');

    $response->assertOk()
        ->assertJsonPath('ip', '8.8.8.8')
        ->assertJsonPath('secure', false);
});
