<?php

declare(strict_types=1);

use App\Actions\ErrorLogs\ExtractCodeSnippetAction;
use Tests\TestCase;

pest()->extend(TestCase::class);

it('returns the lines surrounding the given line', function () {
    $filePath = tempnam(sys_get_temp_dir(), 'snippet');
    file_put_contents($filePath, implode(PHP_EOL, array_map(fn (int $line): string => "line {$line}", range(1, 20))));

    $snippet = app(ExtractCodeSnippetAction::class)->handle($filePath, 10);

    unlink($filePath);

    expect($snippet)->toHaveCount(13)
        ->and($snippet[0])->toBe(['line' => 4, 'code' => 'line 4'])
        ->and($snippet[12])->toBe(['line' => 16, 'code' => 'line 16']);
});

it('clamps the snippet to the file boundaries', function () {
    $filePath = tempnam(sys_get_temp_dir(), 'snippet');
    file_put_contents($filePath, implode(PHP_EOL, ['first', 'second', 'third']));

    $snippet = app(ExtractCodeSnippetAction::class)->handle($filePath, 1);

    unlink($filePath);

    expect(array_column($snippet, 'line'))->toBe([1, 2, 3]);
});

it('returns null when the file cannot be read', function () {
    expect(app(ExtractCodeSnippetAction::class)->handle('/path/that/does/not/exist.php', 1))->toBeNull();
});
