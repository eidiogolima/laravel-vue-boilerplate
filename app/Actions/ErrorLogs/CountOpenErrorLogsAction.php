<?php

declare(strict_types=1);

namespace App\Actions\ErrorLogs;

use App\Models\ErrorLog;

class CountOpenErrorLogsAction
{
    public function handle(): int
    {
        return ErrorLog::query()->whereNull('resolved_at')->count();
    }
}
