<?php

declare(strict_types=1);

namespace App\Http\Requests\Notifications;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ListNotificationsRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'filter' => ['nullable', 'string', 'in:all,unread'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function onlyUnread(): bool
    {
        return $this->validated('filter') === 'unread';
    }
}
