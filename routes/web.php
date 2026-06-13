<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\PollController;
use App\Http\Controllers\Admin\VisitorController;
use App\Http\Controllers\Admin\CertificateController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\LoginDetailController;
use App\Http\Controllers\Admin\PreviousSessionController;
use App\Http\Controllers\Admin\AnnouncementController;

// ==================== PUBLIC ROUTES ====================

// Home/Welcome Page
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Webinar Join Redirect
Route::get('/webinar/join', function () {
    return redirect('https://royalcanin.sociolive.in/');
})->name('webinar.join');

// Thank You Page (after registration)
Route::get('/thank-you', function () {
    return view('thank-you');
})->name('thank.you');

Route::get('api/active-announcements', [App\Http\Controllers\Admin\AnnouncementController::class, 'getActive']);

// ==================== ADMIN ROUTES ====================
Route::prefix('admin')->name('admin.')->group(function () {

    // Guest routes for admin (not logged in)
    Route::middleware('guest:admin')->group(function () {
        Route::get('login', [AdminAuthController::class, 'showLoginForm'])->name('login');
        Route::post('login', [AdminAuthController::class, 'login'])->name('login.submit');
    });

    // Protected admin routes with admin session middleware
    Route::middleware(['auth:admin', 'admin.session'])->group(function () {
        
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');

        // Admin User Management
        Route::resource('admins', AdminUserController::class);
        Route::get('admins/{id}/toggle-status', [AdminUserController::class, 'toggleStatus'])
            ->name('admins.toggle-status');

        // Polls Management
        Route::resource('polls', PollController::class);
        Route::post('polls/{id}/toggle-status', [PollController::class, 'toggleStatus'])->name('polls.toggle-status');

        // Users Management
        Route::get('users', [UserController::class, 'index'])->name('users');
        Route::delete('users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

        // Questions Management
        Route::get('questions', [QuestionController::class, 'index'])->name('questions');
        Route::get('questions/{id}', [QuestionController::class, 'show'])->name('questions.show');
        Route::post('questions/{id}/answer', [QuestionController::class, 'answer'])->name('questions.answer');
        Route::delete('questions/{id}', [QuestionController::class, 'destroy'])->name('questions.destroy');
        Route::post('questions/bulk-delete', [QuestionController::class, 'bulkDelete'])->name('questions.bulk-delete');

        // Login Details Routes
        Route::get('login-details', [LoginDetailController::class, 'index'])->name('login-details.index');
        Route::get('login-details/{id}', [LoginDetailController::class, 'show'])->name('login-details.show');
        Route::get('login-details/export/csv', [LoginDetailController::class, 'export'])->name('login-details.export');
        Route::delete('login-details/{id}', [LoginDetailController::class, 'destroy'])->name('login-details.destroy');
        Route::post('login-details/bulk-delete', [LoginDetailController::class, 'bulkDelete'])->name('login-details.bulk-delete');
        Route::post('login-details/clear-old', [LoginDetailController::class, 'clearOldRecords'])->name('login-details.clear-old');

        // Previous Sessions Routes
        Route::get('previous-sessions', [PreviousSessionController::class, 'index'])->name('previous-sessions.index');
        Route::get('previous-sessions/{id}', [PreviousSessionController::class, 'show'])->name('previous-sessions.show');
        Route::post('previous-sessions/{id}/resend-certificate', [PreviousSessionController::class, 'resendCertificate'])->name('previous-sessions.resend-certificate');
        Route::delete('previous-sessions/{id}', [PreviousSessionController::class, 'destroy'])->name('previous-sessions.destroy');
        Route::post('previous-sessions/bulk-delete', [PreviousSessionController::class, 'bulkDelete'])->name('previous-sessions.bulk-delete');
        Route::post('previous-sessions/clear-old', [PreviousSessionController::class, 'clearOldRecords'])->name('previous-sessions.clear-old');

        // Announcement Routes
Route::resource('announcements', AnnouncementController::class);
Route::get('announcements/{id}/toggle-status', [AnnouncementController::class, 'toggleStatus'])
    ->name('announcements.toggle-status');
Route::get('get-active-announcements', [AnnouncementController::class, 'getActive'])
    ->name('announcements.get-active');
    
    
        });
});

// ==================== AUTH ROUTES ====================
require __DIR__ . '/auth.php';

// ==================== PROTECTED USER ROUTES ====================
Route::middleware('auth')->group(function () {
    // Dashboard/Webcast Pages
    Route::get('/webcast', [PageController::class, 'webcast'])->name('webcast');
    Route::get('/previous-sessions', [PageController::class, 'previousSessions'])->name('previous-sessions');
    Route::post('/send-certificate', [PageController::class, 'sendCertificate'])->name('send-certificate');
    Route::get('/player', [PageController::class, 'player'])->name('player');

    // AJAX Routes for Webcast
    Route::post('/submit-question', [PageController::class, 'submitQuestion'])->name('submit-question');
    Route::post('/get-questions', [PageController::class, 'getQuestions'])->name('get-questions');
    Route::post('/check-poll', [PageController::class, 'checkPoll'])->name('check.poll');
    Route::post('/submit-vote', [PageController::class, 'submitVote'])->name('submit.poll.vote');
    Route::post('/store-reaction', [PageController::class, 'storeReaction'])->name('store-reaction');
    Route::post('/track-activity', [PageController::class, 'trackActivity'])->name('track-activity');
    Route::post('/track-login', [PageController::class, 'trackLogin'])->name('track-login');
    Route::post('/track-logout', [PageController::class, 'trackLogout'])->name('track-logout');

    // Logout
    Route::post('/logout', function () {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/');
    })->name('logout');
});

// ==================== FALLBACK ROUTE (MUST BE LAST) ====================
Route::fallback(function () {
    return redirect()->route('home');
});