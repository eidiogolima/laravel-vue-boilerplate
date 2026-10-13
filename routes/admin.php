<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\ErrorLogController;
use App\Http\Controllers\Admin\UserController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'can:viewAny,'.User::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::redirect('/', '/admin/users')->name('dashboard');

        Route::resource('users', UserController::class)->except('show');

        Route::controller(ErrorLogController::class)
            ->prefix('error-logs')
            ->name('error-logs.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('test-error', 'triggerTestError')->name('test-error');
                Route::get('{errorLog}', 'show')->name('show');
                Route::patch('{errorLog}/resolution', 'updateResolution')->name('resolution');
                Route::delete('{errorLog}', 'destroy')->name('destroy');
            });
    });
