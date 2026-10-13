<?php

declare(strict_types=1);

namespace App\Actions\ErrorLogs;

use App\Models\ErrorLog;
use Illuminate\Support\Carbon;

class SummarizeErrorLogOccurrencesAction
{
    /**
     * @return array{total: int, first_occurred_at: string|null, last_occurred_at: string|null}
     */
    public function handle(ErrorLog $errorLog): array
    {
        $occurrences = ErrorLog::query()->where('hash', $errorLog->hash);

        return [
            'total' => (clone $occurrences)->count(),
            'first_occurred_at' => $this->toIsoDateTime((clone $occurrences)->min('created_at')),
            'last_occurred_at' => $this->toIsoDateTime((clone $occurrences)->max('created_at')),
        ];
    }

    private function toIsoDateTime(mixed $dateTime): ?string
    {
        return is_string($dateTime) ? Carbon::parse($dateTime)->toIso8601String() : null;
    }
}
