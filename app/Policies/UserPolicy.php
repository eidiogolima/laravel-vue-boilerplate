<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $authenticatedUser): bool
    {
        return $authenticatedUser->isAdmin();
    }

    public function create(User $authenticatedUser): bool
    {
        return $authenticatedUser->isAdmin();
    }

    public function update(User $authenticatedUser, User $user): bool
    {
        return $authenticatedUser->isAdmin();
    }

    public function delete(User $authenticatedUser, User $user): bool
    {
        return $authenticatedUser->isAdmin() && $authenticatedUser->isNot($user);
    }
}
