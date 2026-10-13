<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\UserController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'can:viewAny,'.User::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::redirect('/', '/admin/users')->name('dashboard');

        Route::resource('users', UserController::class)->except('show');
    });
