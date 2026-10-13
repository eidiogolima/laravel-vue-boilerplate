<?php

declare(strict_types=1);

use App\Actions\Notifications\ListUserNotificationsAction;
use App\Models\User;
use App\Notifications\SystemAlertNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

pest()->extend(TestCase::class)->use(RefreshDatabase::class);

it('paginates the notifications of the user', function () {
    $user = User::factory()->create();
    User::factory()->create()->notify(new SystemAlertNotification('Other', 'Other user'));

    foreach (range(1, 3) as $index) {
        $user->notify(new SystemAlertNotification("Alert {$index}", 'Message'));
    }

    $notifications = app(ListUserNotificationsAction::class)->handle($user, perPage: 2);

    expect($notifications->total())->toBe(3)
        ->and($notifications->perPage())->toBe(2)
        ->and($notifications->items())->toHaveCount(2);
});

it('lists only unread notifications when requested', function () {
    $user = User::factory()->create();
    $user->notify(new SystemAlertNotification('Read', 'Message'));
    $user->notifications()->update(['read_at' => now()]);
    $user->notify(new SystemAlertNotification('Unread', 'Message'));

    $notifications = app(ListUserNotificationsAction::class)->handle($user, onlyUnread: true);

    expect($notifications->total())->toBe(1)
        ->and($notifications->items()[0]->data['title'])->toBe('Unread');
});
