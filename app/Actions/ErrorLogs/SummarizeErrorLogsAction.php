<?php

declare(strict_types=1);

namespace App\Actions\ErrorLogs;

use App\Models\ErrorLog;

class SummarizeErrorLogsAction
{
    public function __construct(
        private readonly CountOpenErrorLogsAction $countOpenErrorLogs,
    ) {}

    /**
     * @return array{last_24_hours: int, open: int, distinct_open: int, resolved: int, total: int}
     */
    public function handle(): array
    {
        return [
            'last_24_hours' => ErrorLog::query()->where('created_at', '>=', now()->subDay())->count(),
            'open' => $this->countOpenErrorLogs->handle(),
            'distinct_open' => ErrorLog::query()->whereNull('resolved_at')->distinct()->count('hash'),
            'resolved' => ErrorLog::query()->whereNotNull('resolved_at')->count(),
            'total' => ErrorLog::query()->count(),
        ];
    }
}
