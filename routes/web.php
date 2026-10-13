<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
    Route::inertia('components', 'Components')->name('components');
});

require __DIR__.'/admin.php';
require __DIR__.'/notifications.php';
require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
