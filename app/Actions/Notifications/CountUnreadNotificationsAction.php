<?php

declare(strict_types=1);

namespace App\Actions\Notifications;

use App\Models\User;

class CountUnreadNotificationsAction
{
    public function handle(User $user): int
    {
        return $user->unreadNotifications()->count();
    }
}
