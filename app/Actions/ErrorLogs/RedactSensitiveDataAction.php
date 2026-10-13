<?php

declare(strict_types=1);

namespace App\Actions\ErrorLogs;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class RedactSensitiveDataAction
{
    public const string REDACTED = '[redacted]';

    private const string SENSITIVE_KEY_PATTERN = '/pass|senha|token|secret|cvv|card|cartao|authorization|cookie|xsrf|csrf|api[-_]?key/i';

    private const int MAX_STRING_LENGTH = 2000;

    /**
     * @param  array<array-key, mixed>  $data
     * @return array<array-key, mixed>
     */
    public function handle(array $data): array
    {
        $redactedData = [];

        foreach ($data as $key => $value) {
            $redactedData[$key] = $this->isSensitiveKey((string) $key)
                ? self::REDACTED
                : $this->redactValue($value);
        }

        return $redactedData;
    }

    private function isSensitiveKey(string $key): bool
    {
        return preg_match(self::SENSITIVE_KEY_PATTERN, $key) === 1;
    }

    private function redactValue(mixed $value): mixed
    {
        return match (true) {
            $value instanceof UploadedFile => sprintf('[file] %s (%d bytes)', $value->getClientOriginalName(), $value->getSize()),
            is_array($value) => $this->handle($value),
            is_string($value) => Str::limit($value, self::MAX_STRING_LENGTH),
            default => $value,
        };
    }
}
