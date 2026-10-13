<?php

declare(strict_types=1);

use App\Enums\NotificationLevel;
use App\Models\User;
use App\Notifications\SystemAlertNotification;
use Illuminate\Notifications\DatabaseNotification;
use Inertia\Testing\AssertableInertia as Assert;

function sendAlertTo(User $user, string $title = 'Alert', NotificationLevel $level = NotificationLevel::Info): DatabaseNotification
{
    $user->notify(new SystemAlertNotification($title, 'Alert message', $level, '/dashboard'));

    /** @var DatabaseNotification */
    return $user->notifications()->latest()->firstOrFail();
}

test('guests are redirected to the login page', function () {
    $this->get(route('notifications.index'))->assertRedirect(route('login'));
});

test('unverified users cannot access the notifications page', function () {
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)
        ->get(route('notifications.index'))
        ->assertRedirect(route('verification.notice'));
});

test('users see their own notifications', function () {
    $user = User::factory()->create();
    $notification = sendAlertTo($user, 'Server maintenance', NotificationLevel::Warning);
    sendAlertTo(User::factory()->create(), 'Someone else alert');

    $this->actingAs($user)
        ->get(route('notifications.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('notifications/Index')
            ->where('filter', 'all')
            ->has('notifications.data', 1)
            ->where('notifications.data.0.id', $notification->id)
            ->where('notifications.data.0.title', 'Server maintenance')
            ->where('notifications.data.0.message', 'Alert message')
            ->where('notifications.data.0.level', 'warning')
            ->where('notifications.data.0.action_url', '/dashboard')
            ->where('notifications.data.0.is_read', false)
        );
});

test('notifications are paginated', function () {
    $user = User::factory()->create();

    foreach (range(1, 20) as $index) {
        sendAlertTo($user, "Alert {$index}");
    }

    $this->actingAs($user)
        ->get(route('notifications.index', ['page' => 2]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('notifications.data', 5)
            ->where('notifications.meta.current_page', 2)
            ->where('notifications.meta.last_page', 2)
            ->where('notifications.meta.total', 20)
        );
});

test('notifications can be filtered by unread', function () {
    $user = User::factory()->create();
    sendAlertTo($user, 'Read alert')->markAsRead();
    sendAlertTo($user, 'Unread alert');

    $this->actingAs($user)
        ->get(route('notifications.index', ['filter' => 'unread']))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('filter', 'unread')
            ->has('notifications.data', 1)
            ->where('notifications.data.0.title', 'Unread alert')
        );
});

test('the notifications filter must be valid', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->from(route('dashboard'))
        ->get(route('notifications.index', ['filter' => 'invalid', 'page' => 0]))
        ->assertSessionHasErrors(['filter', 'page'])
        ->assertRedirect(route('dashboard'));
});

test('users can mark a notification as read', function () {
    $user = User::factory()->create();
    $notification = sendAlertTo($user);

    $this->actingAs($user)
        ->from(route('notifications.index'))
        ->patch(route('notifications.read', $notification))
        ->assertRedirect(route('notifications.index'));

    expect($notification->fresh()->read_at)->not->toBeNull();
});

test('users cannot mark notifications of other users as read', function () {
    $notification = sendAlertTo(User::factory()->create());

    $this->actingAs(User::factory()->create())
        ->patch(route('notifications.read', $notification))
        ->assertForbidden();

    expect($notification->fresh()->read_at)->toBeNull();
});

test('marking a missing notification as read returns not found', function () {
    $this->actingAs(User::factory()->create())
        ->patch(route('notifications.read', 'missing-notification'))
        ->assertNotFound();
});

test('users can mark all notifications as read', function () {
    $user = User::factory()->create();
    sendAlertTo($user);
    sendAlertTo($user);
    $otherUserNotification = sendAlertTo(User::factory()->create());

    $this->actingAs($user)
        ->from(route('notifications.index'))
        ->post(route('notifications.read-all'))
        ->assertRedirect(route('notifications.index'))
        ->assertInertiaFlash('toast', ['type' => 'success', 'message' => __('messages.notifications.all_marked_as_read')]);

    expect($user->unreadNotifications()->count())->toBe(0)
        ->and($otherUserNotification->fresh()->read_at)->toBeNull();
});

test('users can delete a notification', function () {
    $user = User::factory()->create();
    $notification = sendAlertTo($user);

    $this->actingAs($user)
        ->from(route('notifications.index'))
        ->delete(route('notifications.destroy', $notification))
        ->assertRedirect(route('notifications.index'))
        ->assertInertiaFlash('toast', ['type' => 'success', 'message' => __('messages.notifications.deleted')]);

    expect($notification->fresh())->toBeNull();
});

test('users cannot delete notifications of other users', function () {
    $notification = sendAlertTo(User::factory()->create());

    $this->actingAs(User::factory()->create())
        ->delete(route('notifications.destroy', $notification))
        ->assertForbidden();

    expect($notification->fresh())->not->toBeNull();
});

test('pages share the unread notifications count', function () {
    $user = User::factory()->create();
    sendAlertTo($user);
    sendAlertTo($user)->markAsRead();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertInertia(fn (Assert $page) => $page
            ->where('unreadNotificationsCount', 1)
            ->missing('recentNotifications')
        );
});

test('recent notifications are loaded on partial reloads', function () {
    $user = User::factory()->create();

    foreach (range(1, 7) as $index) {
        sendAlertTo($user, "Alert {$index}");
    }

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertInertia(fn (Assert $page) => $page
            ->reloadOnly('recentNotifications', fn (Assert $reload) => $reload
                ->has('recentNotifications.data', 5)
                ->where('recentNotifications.meta.total', 7)
            )
        );
});
