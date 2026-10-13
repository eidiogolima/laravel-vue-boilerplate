<?php

declare(strict_types=1);

use App\Actions\ErrorLogs\RedactSensitiveDataAction;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

pest()->extend(TestCase::class);

it('redacts sensitive keys at any depth', function () {
    $redactedData = app(RedactSensitiveDataAction::class)->handle([
        'name' => 'Maria',
        'password' => 'secret',
        'password_confirmation' => 'secret',
        'profile' => ['api_key' => 'abc', 'city' => 'Recife'],
        'authorization' => 'Bearer token',
    ]);

    expect($redactedData)->toBe([
        'name' => 'Maria',
        'password' => RedactSensitiveDataAction::REDACTED,
        'password_confirmation' => RedactSensitiveDataAction::REDACTED,
        'profile' => ['api_key' => RedactSensitiveDataAction::REDACTED, 'city' => 'Recife'],
        'authorization' => RedactSensitiveDataAction::REDACTED,
    ]);
});

it('describes uploaded files instead of storing them', function () {
    $redactedData = app(RedactSensitiveDataAction::class)->handle([
        'document' => UploadedFile::fake()->create('contract.pdf', 2),
    ]);

    expect($redactedData['document'])->toBe('[file] contract.pdf (2048 bytes)');
});

it('truncates long strings and keeps other scalar values', function () {
    $redactedData = app(RedactSensitiveDataAction::class)->handle([
        'notes' => str_repeat('a', 3000),
        'quantity' => 3,
        'active' => true,
    ]);

    expect(mb_strlen($redactedData['notes']))->toBe(2003)
        ->and($redactedData['quantity'])->toBe(3)
        ->and($redactedData['active'])->toBeTrue();
});
