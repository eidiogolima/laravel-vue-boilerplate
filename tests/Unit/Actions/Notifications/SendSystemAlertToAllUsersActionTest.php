<?php

declare(strict_types=1);

use App\Actions\Notifications\SendSystemAlertToAllUsersAction;
use App\Models\User;
use App\Notifications\SystemAlertNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

pest()->extend(TestCase::class)->use(RefreshDatabase::class);

it('sends the alert to every user and returns the recipients count', function () {
    Notification::fake();
    $users = User::factory()->count(3)->create();

    $recipientsCount = app(SendSystemAlertToAllUsersAction::class)->handle(new SystemAlertNotification('Hello', 'Everyone'));

    expect($recipientsCount)->toBe(3);
    Notification::assertSentTo($users, SystemAlertNotification::class);
});
