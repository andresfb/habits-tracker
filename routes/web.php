<?php

declare(strict_types=1);

use App\Http\Controllers\LoginAccessController;
use App\Http\Controllers\RegisterController;
use Livewire\Volt\Volt;

Route::middleware(['throttle:login'])->group(function () {
    Volt::route('/login', 'auth.login')
        ->name('login');

    Volt::route('/invitation', 'auth.invitation')
        ->name('invitation');

    Route::get('/login/{email}', LoginAccessController::class)
        ->middleware('signed')
        ->name('login.auth');
});

Route::middleware(['invitation', 'throttle:invite'])->group(function (): void {
    Route::get('/register', RegisterController::class)
        ->name('register');
});

Route::get('/logout', static function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->route('login');
});

Volt::route('/sent', 'auth.invite-request-sent')
    ->name('invite.sent');

// Protected routes here
Route::middleware(['auth', 'verified', 'registered'])->group(function (): void {
    Volt::route('/', 'index')
        ->name('home');

    Volt::route('/category', 'categories.index')
        ->name('categories');

    Volt::route('/unit', 'units.index')
        ->name('units');

    Volt::route('/period', 'periods.index')
        ->name('periods');

    Volt::route('/habit', 'habits.index')
        ->name('habits');
});
