<?php

declare(strict_types=1);

use App\Actions\Notifications\DeleteNotificationAction;
use App\Models\User;
use App\Notifications\SystemAlertNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

pest()->extend(TestCase::class)->use(RefreshDatabase::class);

it('deletes the notification', function () {
    $user = User::factory()->create();
    $user->notify(new SystemAlertNotification('Alert', 'Message'));

    app(DeleteNotificationAction::class)->handle($user->notifications()->sole());

    expect($user->notifications()->count())->toBe(0);
});
