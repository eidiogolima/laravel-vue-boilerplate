<?php

declare(strict_types=1);

use App\Actions\ErrorLogs\ExtractStackFramesAction;
use Tests\TestCase;

pest()->extend(TestCase::class);

it('extracts project relative frames and flags vendor frames', function () {
    $frames = app(ExtractStackFramesAction::class)->handle(new RuntimeException('Failure'));

    $vendorFrame = collect($frames)->firstWhere('is_vendor', true);

    expect($frames)->not->toBeEmpty()
        ->and($frames[0])->toHaveKeys(['file', 'line', 'function', 'is_vendor'])
        ->and($vendorFrame['file'])->toStartWith('vendor/');
});

it('describes class methods with their call type', function () {
    $exception = (new class
    {
        public function fail(): RuntimeException
        {
            return new RuntimeException('Failure');
        }
    })->fail();

    $frames = app(ExtractStackFramesAction::class)->handle($exception);

    expect($frames[0]['function'])->toContain('->');
});

it('limits the number of frames', function () {
    $buildNestedException = function (int $depth) use (&$buildNestedException): RuntimeException {
        return $depth === 0 ? new RuntimeException('Deep failure') : $buildNestedException($depth - 1);
    };

    expect(app(ExtractStackFramesAction::class)->handle($buildNestedException(100)))->toHaveCount(60);
});
