<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AIStressController;

// Public routes (no authentication required)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Test route
Route::get('/test', function() {
    return response()->json([
        'message' => 'API is working!',
        'timestamp' => now()->toDateTimeString()
    ]);
});

// Protected routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
     Route::get('/ai/models', [AIStressController::class, 'getAvailableModels']);
     Route::get('/franklin/status', [AIStressController::class, 'getFranklinStatus']);
    Route::post('/ai/stress-test', [AIStressController::class, 'stressTestModel']);
     Route::get('/franklin/metrics', [AIStressController::class, 'getFranklinMetrics']);
    Route::get('/franklin/capabilities', [AIStressController::class, 'getFranklinCapabilities']);
    Route::get('/analytics', [AIStressController::class, 'getAnalytics']);
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    
    // AI Challenges
    Route::post('/ai/challenge', [AIStressController::class, 'processChallenge']);
    Route::get('/ai/metrics', [AIStressController::class, 'getMetrics']);
    
    // Leaderboard and Stats
    Route::get('/leaderboard', [AIStressController::class, 'getLeaderboard']);
    Route::get('/user/stats', [AIStressController::class, 'getUserStats']);
});