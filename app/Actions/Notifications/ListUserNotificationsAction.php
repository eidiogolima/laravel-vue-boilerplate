<?php

declare(strict_types=1);

namespace App\Actions\Notifications;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Notifications\DatabaseNotification;

class ListUserNotificationsAction
{
    /**
     * @return LengthAwarePaginator<int, DatabaseNotification>
     */
    public function handle(User $user, bool $onlyUnread = false, int $perPage = 15): LengthAwarePaginator
    {
        return $user->notifications()
            ->when($onlyUnread, fn ($query) => $query->whereNull('read_at'))
            ->paginate($perPage)
            ->withQueryString();
    }
}
