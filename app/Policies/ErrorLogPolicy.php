<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ErrorLog;
use App\Models\User;

class ErrorLogPolicy
{
    public function viewAny(User $authenticatedUser): bool
    {
        return $authenticatedUser->isAdmin();
    }

    public function view(User $authenticatedUser, ErrorLog $errorLog): bool
    {
        return $authenticatedUser->isAdmin();
    }

    public function update(User $authenticatedUser, ErrorLog $errorLog): bool
    {
        return $authenticatedUser->isAdmin();
    }

    public function delete(User $authenticatedUser, ErrorLog $errorLog): bool
    {
        return $authenticatedUser->isAdmin();
    }

    public function triggerTestError(User $authenticatedUser): bool
    {
        return $authenticatedUser->isAdmin();
    }
}
