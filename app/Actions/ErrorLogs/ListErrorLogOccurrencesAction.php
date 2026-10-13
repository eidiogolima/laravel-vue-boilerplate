<?php

declare(strict_types=1);

namespace App\Actions\ErrorLogs;

use App\Models\ErrorLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListErrorLogOccurrencesAction
{
    public const string PAGE_NAME = 'occurrences_page';

    /**
     * @return LengthAwarePaginator<int, ErrorLog>
     */
    public function handle(ErrorLog $errorLog, int $perPage = 10): LengthAwarePaginator
    {
        return ErrorLog::query()
            ->with('user:id,name')
            ->where('hash', $errorLog->hash)
            ->whereKeyNot($errorLog->getKey())
            ->latest('id')
            ->paginate($perPage, pageName: self::PAGE_NAME)
            ->withQueryString();
    }
}
