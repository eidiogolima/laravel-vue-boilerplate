<?php

declare(strict_types=1);

namespace App\Actions\Notifications;

use Illuminate\Notifications\DatabaseNotification;

class DeleteNotificationAction
{
    public function handle(DatabaseNotification $notification): void
    {
        $notification->delete();
    }
}
