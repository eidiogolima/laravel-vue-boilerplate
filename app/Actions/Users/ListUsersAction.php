<?php

declare(strict_types=1);

namespace App\Actions\Users;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class ListUsersAction
{
    /**
     * @return LengthAwarePaginator<int, User>
     */
    public function handle(?string $search = null, ?UserRole $role = null, int $perPage = 15): LengthAwarePaginator
    {
        return User::query()
            ->when(filled($search), function (Builder $query) use ($search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->whereLike('name', "%{$search}%")
                        ->orWhereLike('email', "%{$search}%");
                });
            })
            ->when($role !== null, fn (Builder $query): Builder => $query->where('role', $role))
            ->latest()
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }
}
