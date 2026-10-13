<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\Users\CreateUserAction;
use App\Actions\Users\DeleteUserAction;
use App\Actions\Users\ListUsersAction;
use App\Actions\Users\UpdateUserAction;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Users\CreateUserRequest;
use App\Http\Requests\Admin\Users\DestroyUserRequest;
use App\Http\Requests\Admin\Users\EditUserRequest;
use App\Http\Requests\Admin\Users\IndexUserRequest;
use App\Http\Requests\Admin\Users\StoreUserRequest;
use App\Http\Requests\Admin\Users\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(IndexUserRequest $request, ListUsersAction $listUsers): Response
    {
        return Inertia::render('admin/users/Index', [
            'users' => UserResource::collection($listUsers->handle($request->search(), $request->role())),
            'filters' => [
                'search' => $request->search() ?? '',
                'role' => $request->role()->value ?? '',
            ],
            'roles' => array_column(UserRole::cases(), 'value'),
        ]);
    }

    public function create(CreateUserRequest $request): Response
    {
        return Inertia::render('admin/users/Create', [
            'roles' => array_column(UserRole::cases(), 'value'),
        ]);
    }

    public function store(StoreUserRequest $request, CreateUserAction $createUser): RedirectResponse
    {
        $createUser->handle(
            $request->string('name')->toString(),
            $request->string('email')->toString(),
            $request->string('password')->toString(),
            $request->role(),
        );

        Inertia::flash('toast', ['type' => 'success', 'message' => __('messages.user.created')]);

        return to_route('admin.users.index');
    }

    public function edit(EditUserRequest $request, User $user): Response
    {
        return Inertia::render('admin/users/Edit', [
            'user' => UserResource::make($user)->resolve($request),
            'roles' => array_column(UserRole::cases(), 'value'),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user, UpdateUserAction $updateUser): RedirectResponse
    {
        $updateUser->handle(
            $user,
            $request->string('name')->toString(),
            $request->string('email')->toString(),
            $request->role(),
            $request->newPassword(),
        );

        Inertia::flash('toast', ['type' => 'success', 'message' => __('messages.user.updated')]);

        return to_route('admin.users.index');
    }

    public function destroy(DestroyUserRequest $request, User $user, DeleteUserAction $deleteUser): RedirectResponse
    {
        $deleteUser->handle($user);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('messages.user.deleted')]);

        return to_route('admin.users.index');
    }
}
