<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;

// ==================== GUEST ROUTES ====================
Route::middleware('guest')->group(function () {
    // Registration Routes
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store'])->name('register.store');

    // Login Routes
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store'])->name('login.store');

    // Create/Reset Password Routes (email -> OTP -> new password)
    Route::get('create-password', [PasswordController::class, 'create'])->name('password.create');
    Route::post('create-password/send-otp', [PasswordController::class, 'sendOtp'])
        ->middleware('throttle:6,1')
        ->name('password.sendOtp');
    Route::post('create-password/verify-otp', [PasswordController::class, 'verifyOtp'])
        ->middleware('throttle:10,1')
        ->name('password.verifyOtp');
    Route::post('create-password/set', [PasswordController::class, 'setPassword'])->name('password.set');

    // AJAX Routes
    Route::get('/fetch-cities', [RegisteredUserController::class, 'fetchCities'])->name('fetch.cities');
});
