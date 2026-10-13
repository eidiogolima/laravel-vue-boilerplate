<?php

declare(strict_types=1);

namespace App\Actions\ErrorLogs;

use App\Models\ErrorLog;

class UpdateErrorLogResolutionAction
{
    public function handle(ErrorLog $errorLog, bool $isResolved): void
    {
        ErrorLog::query()
            ->where('hash', $errorLog->hash)
            ->update(['resolved_at' => $isResolved ? now() : null]);
    }
}
