<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Actions\Notifications\SendSystemAlertAction;
use App\Enums\NotificationLevel;
use App\Models\User;
use App\Notifications\SystemAlertNotification;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(SendSystemAlertAction $sendSystemAlert): void
    {
        $user = User::factory()->admin()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $sendSystemAlert->handle($user, new SystemAlertNotification(
            title: __('messages.notifications.samples.welcome.title'),
            message: __('messages.notifications.samples.welcome.message'),
            level: NotificationLevel::Success,
            actionUrl: route('dashboard', absolute: false),
        ));

        $sendSystemAlert->handle($user, new SystemAlertNotification(
            title: __('messages.notifications.samples.maintenance.title'),
            message: __('messages.notifications.samples.maintenance.message'),
            level: NotificationLevel::Warning,
        ));
    }
}
