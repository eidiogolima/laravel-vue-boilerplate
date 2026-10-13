<?php

declare(strict_types=1);

namespace App\Actions\ErrorLogs;

use App\Models\ErrorLog;

class DeleteErrorLogAction
{
    public function handle(ErrorLog $errorLog): void
    {
        $errorLog->delete();
    }
}
