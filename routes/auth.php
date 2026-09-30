<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\GoogleAuthenticationController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->prefix('auth/google')->name('auth.google.')->group(function () {
    Route::get('redirect', [GoogleAuthenticationController::class, 'redirect'])->name('redirect');
    Route::get('callback', [GoogleAuthenticationController::class, 'callback'])->name('callback');
});
