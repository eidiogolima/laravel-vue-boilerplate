<?php

declare(strict_types=1);

namespace App\Actions\ErrorLogs;

use Throwable;

class ExtractStackFramesAction
{
    private const int MAX_FRAMES = 60;

    public function __construct(
        private readonly MakeProjectRelativePathAction $makeProjectRelativePath,
    ) {}

    /**
     * @return array<int, array{file: string|null, line: int|null, function: string, is_vendor: bool}>
     */
    public function handle(Throwable $exception): array
    {
        return collect($exception->getTrace())
            ->take(self::MAX_FRAMES)
            ->map(fn (array $frame): array => [
                'file' => isset($frame['file']) ? $this->makeProjectRelativePath->handle($frame['file']) : null,
                'line' => $frame['line'] ?? null,
                'function' => isset($frame['class'])
                    ? $frame['class'].($frame['type'] ?? '::').$frame['function']
                    : $frame['function'],
                'is_vendor' => $this->isVendorFile($frame['file'] ?? null),
            ])
            ->values()
            ->all();
    }

    private function isVendorFile(?string $filePath): bool
    {
        return $filePath === null
            || str_contains($filePath, DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR);
    }
}
