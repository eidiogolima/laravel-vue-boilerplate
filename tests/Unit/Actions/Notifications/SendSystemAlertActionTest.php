<?php

declare(strict_types=1);

use App\Actions\Notifications\SendSystemAlertAction;
use App\Enums\NotificationLevel;
use App\Models\User;
use App\Notifications\SystemAlertNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

pest()->extend(TestCase::class)->use(RefreshDatabase::class);

it('stores the alert in the notification center of the recipients', function () {
    $users = User::factory()->count(2)->create();

    app(SendSystemAlertAction::class)->handle($users, new SystemAlertNotification(
        title: 'Maintenance',
        message: 'Offline tonight.',
        level: NotificationLevel::Error,
        actionUrl: '/status',
    ));

    foreach ($users as $user) {
        expect($user->notifications()->sole()->data)->toBe([
            'title' => 'Maintenance',
            'message' => 'Offline tonight.',
            'level' => 'error',
            'action_url' => '/status',
        ]);
    }
});
