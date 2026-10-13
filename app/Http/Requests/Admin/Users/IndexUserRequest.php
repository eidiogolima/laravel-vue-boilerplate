<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Users;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class IndexUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('viewAny', User::class) ?? false;
    }

    /**
     * @return array<string, array<int, ValidationRule|Enum|string>>
     */
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'role' => ['nullable', Rule::enum(UserRole::class)],
        ];
    }

    public function search(): ?string
    {
        return $this->string('search')->trim()->toString() ?: null;
    }

    public function role(): ?UserRole
    {
        return $this->enum('role', UserRole::class);
    }
}
