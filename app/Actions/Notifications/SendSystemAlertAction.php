<?php

declare(strict_types=1);

namespace App\Actions\Notifications;

use App\Models\User;
use App\Notifications\SystemAlertNotification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Notification;

class SendSystemAlertAction
{
    /**
     * @param  User|Collection<int, User>  $recipients
     */
    public function handle(User|Collection $recipients, SystemAlertNotification $alert): void
    {
        Notification::send($recipients, $alert);
    }
}
