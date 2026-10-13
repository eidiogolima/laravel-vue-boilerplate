<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Users;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class EditUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->managedUser()) ?? false;
    }

    /**
     * @return array<string, array<int, ValidationRule|string>>
     */
    public function rules(): array
    {
        return [];
    }

    public function managedUser(): User
    {
        /** @var User */
        return $this->route('user');
    }
}
