<?php

declare(strict_types=1);

use App\Actions\Notifications\MarkNotificationAsReadAction;
use App\Models\User;
use App\Notifications\SystemAlertNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

pest()->extend(TestCase::class)->use(RefreshDatabase::class);

it('marks the notification as read', function () {
    $user = User::factory()->create();
    $user->notify(new SystemAlertNotification('Alert', 'Message'));
    $notification = $user->notifications()->sole();

    app(MarkNotificationAsReadAction::class)->handle($notification);

    expect($notification->fresh()->read_at)->not->toBeNull();
});
