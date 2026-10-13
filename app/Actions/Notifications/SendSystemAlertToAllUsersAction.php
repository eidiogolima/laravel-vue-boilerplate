<?php

declare(strict_types=1);

namespace App\Actions\Notifications;

use App\Models\User;
use App\Notifications\SystemAlertNotification;
use Illuminate\Database\Eloquent\Collection;

class SendSystemAlertToAllUsersAction
{
    private const int RECIPIENTS_PER_BATCH = 500;

    public function __construct(private readonly SendSystemAlertAction $sendSystemAlert) {}

    public function handle(SystemAlertNotification $alert): int
    {
        $recipientsCount = 0;

        User::query()->chunkById(self::RECIPIENTS_PER_BATCH, function (Collection $users) use ($alert, &$recipientsCount): void {
            $this->sendSystemAlert->handle($users, $alert);
            $recipientsCount += $users->count();
        });

        return $recipientsCount;
    }
}
