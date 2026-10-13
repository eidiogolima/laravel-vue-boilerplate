<?php

declare(strict_types=1);

namespace App\Actions\Notifications;

use Illuminate\Notifications\DatabaseNotification;

class MarkNotificationAsReadAction
{
    public function handle(DatabaseNotification $notification): void
    {
        $notification->markAsRead();
    }
}
