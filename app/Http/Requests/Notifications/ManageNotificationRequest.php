<?php

declare(strict_types=1);

namespace App\Http\Requests\Notifications;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Notifications\DatabaseNotification;

class ManageNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $notification = $this->notification();

        return $notification->notifiable_type === $this->user()?->getMorphClass()
            && (int) $notification->notifiable_id === $this->user()->getKey();
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [];
    }

    public function notification(): DatabaseNotification
    {
        /** @var DatabaseNotification $notification */
        $notification = $this->route('notification');

        return $notification;
    }
}
