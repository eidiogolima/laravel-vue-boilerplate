<?php

declare(strict_types=1);

use App\Actions\Notifications\MarkAllNotificationsAsReadAction;
use App\Models\User;
use App\Notifications\SystemAlertNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

pest()->extend(TestCase::class)->use(RefreshDatabase::class);

it('marks every unread notification of the user as read', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $user->notify(new SystemAlertNotification('First', 'Message'));
    $user->notify(new SystemAlertNotification('Second', 'Message'));
    $otherUser->notify(new SystemAlertNotification('Other', 'Message'));

    $markedCount = app(MarkAllNotificationsAsReadAction::class)->handle($user);

    expect($markedCount)->toBe(2)
        ->and($user->unreadNotifications()->count())->toBe(0)
        ->and($otherUser->unreadNotifications()->count())->toBe(1);
});
