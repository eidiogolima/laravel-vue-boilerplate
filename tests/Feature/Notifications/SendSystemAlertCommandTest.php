<?php

declare(strict_types=1);

use App\Models\User;
use App\Notifications\SystemAlertNotification;
use Illuminate\Support\Facades\Notification;

test('the system alert is sent to every user when no recipient is given', function () {
    Notification::fake();
    $users = User::factory()->count(3)->create();

    $this->artisan('notifications:send-system-alert', [
        'title' => 'Maintenance',
        'message' => 'The system will be offline tonight.',
        '--level' => 'warning',
        '--url' => '/dashboard',
    ])
        ->expectsOutput(__('messages.notifications.system_alert_sent', ['count' => 3]))
        ->assertSuccessful();

    Notification::assertSentTo($users, SystemAlertNotification::class, fn (SystemAlertNotification $alert): bool => $alert->title === 'Maintenance'
        && $alert->level->value === 'warning'
        && $alert->actionUrl === '/dashboard');
});

test('the system alert is sent only to the given recipients', function () {
    Notification::fake();
    $recipient = User::factory()->create();
    $otherUser = User::factory()->create();

    $this->artisan('notifications:send-system-alert', [
        'title' => 'Hello',
        'message' => 'Only for you.',
        '--user' => [$recipient->email],
    ])->assertSuccessful();

    Notification::assertSentTo($recipient, SystemAlertNotification::class);
    Notification::assertNotSentTo($otherUser, SystemAlertNotification::class);
});

test('the system alert level must be valid', function () {
    Notification::fake();
    User::factory()->create();

    $this->artisan('notifications:send-system-alert', [
        'title' => 'Hello',
        'message' => 'Invalid level.',
        '--level' => 'critical',
    ])->assertFailed();

    Notification::assertNothingSent();
});
