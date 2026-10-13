<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Users;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\In;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    use PasswordValidationRules, ProfileValidationRules;

    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->managedUser()) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            ...$this->profileRules($this->managedUser()->id),
            'password' => ['nullable', 'string', Password::default(), 'confirmed'],
            'role' => $this->roleRules(),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'role.in' => __('errors.user.cannot_change_own_role'),
        ];
    }

    public function managedUser(): User
    {
        /** @var User */
        return $this->route('user');
    }

    public function role(): UserRole
    {
        return $this->enum('role', UserRole::class) ?? UserRole::User;
    }

    public function newPassword(): ?string
    {
        return $this->string('password')->toString() ?: null;
    }

    /**
     * @return array<int, ValidationRule|In|Enum|string>
     */
    private function roleRules(): array
    {
        $rules = ['required', Rule::enum(UserRole::class)];

        if ($this->isUpdatingOwnAccount()) {
            $rules[] = Rule::in([UserRole::Admin->value]);
        }

        return $rules;
    }

    private function isUpdatingOwnAccount(): bool
    {
        return $this->user()?->is($this->managedUser()) ?? false;
    }
}
