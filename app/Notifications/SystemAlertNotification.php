<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Enums\NotificationLevel;
use App\Models\User;
use Illuminate\Notifications\Notification;

class SystemAlertNotification extends Notification
{
    public function __construct(
        public readonly string $title,
        public readonly string $message,
        public readonly NotificationLevel $level = NotificationLevel::Info,
        public readonly ?string $actionUrl = null,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(User $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array{title: string, message: string, level: string, action_url: string|null}
     */
    public function toArray(User $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'level' => $this->level->value,
            'action_url' => $this->actionUrl,
        ];
    }
}
