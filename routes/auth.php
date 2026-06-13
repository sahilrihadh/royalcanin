<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// ==================== GUEST ROUTES ====================
Route::middleware('guest')->group(function () {
    // Registration Routes
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store'])->name('register.store');

    // Login Routes
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store'])->name('login.store');

    // AJAX Routes
    Route::get('/fetch-cities', [RegisteredUserController::class, 'fetchCities'])->name('fetch.cities');
});

// ==================== CUSTOM LOGIN PROCESS (AJAX) ====================
Route::post('/login-process', function (LoginRequest $request) {
    try {
        $request->authenticate();
        $request->session()->regenerate();

        if (!Auth::check()) {
            throw new \Exception('Authentication failed');
        }

        return response()->json([
            'success' => true,
            'message' => 'Login successful!',
            'redirect_url' => route('webcast'),
            'user' => Auth::user()->email_id
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 401);
    }
})->name('login.process')->middleware('guest');
