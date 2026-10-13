<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Notifications\SendSystemAlertAction;
use App\Actions\Notifications\SendSystemAlertToAllUsersAction;
use App\Enums\NotificationLevel;
use App\Models\User;
use App\Notifications\SystemAlertNotification;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('notifications:send-system-alert
    {title : The alert title}
    {message : The alert message}
    {--level=info : The alert level (info, success, warning, error)}
    {--url= : An optional URL the alert links to}
    {--user=* : The e-mail of each recipient; every user receives the alert when omitted}')]
#[Description('Send a system alert to the notification center of the users')]
class SendSystemAlertCommand extends Command
{
    public function handle(SendSystemAlertAction $sendSystemAlert, SendSystemAlertToAllUsersAction $sendSystemAlertToAllUsers): int
    {
        $level = NotificationLevel::tryFrom((string) $this->option('level'));

        if ($level === null) {
            $this->error(__('errors.notifications.invalid_level', [
                'levels' => implode(', ', array_column(NotificationLevel::cases(), 'value')),
            ]));

            return self::INVALID;
        }

        $url = $this->option('url');

        $alert = new SystemAlertNotification(
            title: (string) $this->argument('title'),
            message: (string) $this->argument('message'),
            level: $level,
            actionUrl: is_string($url) && $url !== '' ? $url : null,
        );

        /** @var array<int, string> $recipientEmails */
        $recipientEmails = $this->option('user');

        if ($recipientEmails === []) {
            $recipientsCount = $sendSystemAlertToAllUsers->handle($alert);
        } else {
            $recipients = User::query()->whereIn('email', $recipientEmails)->get();
            $sendSystemAlert->handle($recipients, $alert);
            $recipientsCount = $recipients->count();
        }

        $this->info(__('messages.notifications.system_alert_sent', ['count' => $recipientsCount]));

        return self::SUCCESS;
    }
}
