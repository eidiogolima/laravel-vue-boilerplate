<?php

declare(strict_types=1);

namespace App\Actions\ErrorLogs;

use Illuminate\Support\Str;

class ExtractCodeSnippetAction
{
    private const int SURROUNDING_LINES = 6;

    private const int MAX_LINE_LENGTH = 300;

    /**
     * @return array<int, array{line: int, code: string}>|null
     */
    public function handle(string $filePath, int $line): ?array
    {
        if (! is_file($filePath) || ! is_readable($filePath)) {
            return null;
        }

        $fileLines = file($filePath, FILE_IGNORE_NEW_LINES);

        if ($fileLines === false || $fileLines === []) {
            return null;
        }

        $firstLine = max(1, $line - self::SURROUNDING_LINES);
        $lastLine = min(count($fileLines), $line + self::SURROUNDING_LINES);

        $snippet = [];

        for ($currentLine = $firstLine; $currentLine <= $lastLine; $currentLine++) {
            $snippet[] = [
                'line' => $currentLine,
                'code' => Str::limit($fileLines[$currentLine - 1], self::MAX_LINE_LENGTH),
            ];
        }

        return $snippet;
    }
}
