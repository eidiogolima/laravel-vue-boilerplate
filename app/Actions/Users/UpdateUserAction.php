<?php

declare(strict_types=1);

namespace App\Actions\Users;

use App\Enums\UserRole;
use App\Models\User;

class UpdateUserAction
{
    public function handle(User $user, string $name, string $email, UserRole $role, ?string $password = null): User
    {
        $user->fill([
            'name' => $name,
            'email' => $email,
            'role' => $role,
        ]);

        if (filled($password)) {
            $user->password = $password;
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return $user;
    }
}
