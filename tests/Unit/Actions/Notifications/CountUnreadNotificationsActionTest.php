<?php

declare(strict_types=1);

use App\Actions\Notifications\CountUnreadNotificationsAction;
use App\Models\User;
use App\Notifications\SystemAlertNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

pest()->extend(TestCase::class)->use(RefreshDatabase::class);

it('counts only the unread notifications of the user', function () {
    $user = User::factory()->create();
    $user->notify(new SystemAlertNotification('Read', 'Message'));
    $user->notifications()->update(['read_at' => now()]);
    $user->notify(new SystemAlertNotification('Unread', 'Message'));
    User::factory()->create()->notify(new SystemAlertNotification('Other', 'Message'));

    expect(app(CountUnreadNotificationsAction::class)->handle($user))->toBe(1);
});
