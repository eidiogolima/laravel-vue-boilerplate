<?php

declare(strict_types=1);

namespace App\Actions\ErrorLogs;

use App\Enums\ErrorLogResolution;
use App\Enums\HttpStatusCodeRange;
use App\Enums\SortOrder;
use App\Models\ErrorLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class ListErrorLogsAction
{
    /**
     * @return LengthAwarePaginator<int, ErrorLog>
     */
    public function handle(
        ?string $search = null,
        ErrorLogResolution $resolution = ErrorLogResolution::Open,
        ?HttpStatusCodeRange $statusCodeRange = null,
        SortOrder $sortOrder = SortOrder::Newest,
        int $perPage = 20,
    ): LengthAwarePaginator {
        return ErrorLog::query()
            ->with('user:id,name')
            ->when($resolution === ErrorLogResolution::Open, fn (Builder $query): Builder => $query->whereNull('resolved_at'))
            ->when($resolution === ErrorLogResolution::Resolved, fn (Builder $query): Builder => $query->whereNotNull('resolved_at'))
            ->when($statusCodeRange !== null, fn (Builder $query): Builder => $query->whereBetween('status_code', $statusCodeRange->bounds()))
            ->when(filled($search), function (Builder $query) use ($search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->whereLike('message', "%{$search}%")
                        ->orWhereLike('exception', "%{$search}%")
                        ->orWhereLike('url', "%{$search}%")
                        ->orWhereLike('file', "%{$search}%");
                });
            })
            ->orderBy('id', $sortOrder === SortOrder::Oldest ? 'asc' : 'desc')
            ->paginate($perPage)
            ->withQueryString();
    }
}
