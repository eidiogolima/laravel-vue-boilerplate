<?php

declare(strict_types=1);

namespace App\Actions\Notifications;

use App\Models\User;

class MarkAllNotificationsAsReadAction
{
    public function handle(User $user): int
    {
        return $user->unreadNotifications()->update(['read_at' => now()]);
    }
}
