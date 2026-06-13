<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PollController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\WebinarController;

// ==================== PUBLIC API ROUTES ====================
Route::get('/test-message', function () {
    return response()->json(['message' => 'Axios is working perfectly! 🎉']);
});

// ==================== PROTECTED API ROUTES ====================
Route::middleware('auth:sanctum')->group(function () {
    // Poll routes
    Route::post('/poll/vote', [PollController::class, 'vote']);
    Route::get('/poll/results/{pollId}', [PollController::class, 'getResults']);

    // Question routes
    Route::post('/question/ask', [QuestionController::class, 'store']);
    Route::get('/questions/{webinarSessionId}', [QuestionController::class, 'index']);

    // Webinar tracking
    Route::post('/webinar/track-start', [WebinarController::class, 'trackStart']);
    Route::post('/webinar/track-complete', [WebinarController::class, 'trackComplete']);
});

// ==================== OPTIONAL: If you want API versioning ====================
// Route::prefix('v1')->group(function () {
//     // Your versioned API routes here
// });