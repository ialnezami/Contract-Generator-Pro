<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ContractController;
use App\Http\Controllers\Api\TemplateController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Template routes (public access)
Route::get('/templates', [TemplateController::class, 'index']);
Route::get('/templates/categories', [TemplateController::class, 'categories']);
Route::get('/templates/popular', [TemplateController::class, 'popular']);
Route::get('/templates/highly-rated', [TemplateController::class, 'highlyRated']);
Route::get('/templates/{template}', [TemplateController::class, 'show']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // User profile
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::put('/change-password', [AuthController::class, 'changePassword']);

    // Contract routes
    Route::prefix('contracts')->group(function () {
        Route::get('/', [ContractController::class, 'index']);
        Route::post('/', [ContractController::class, 'store']);
        Route::get('/statistics', [ContractController::class, 'statistics']);
        Route::get('/{contract}', [ContractController::class, 'show']);
        Route::put('/{contract}', [ContractController::class, 'update']);
        Route::delete('/{contract}', [ContractController::class, 'destroy']);
        Route::post('/{contract}/generate-pdf', [ContractController::class, 'generatePdf']);
        Route::post('/{contract}/sign', [ContractController::class, 'sign']);
    });

    // Template management routes
    Route::prefix('templates')->group(function () {
        Route::post('/', [TemplateController::class, 'store']);
        Route::put('/{template}', [TemplateController::class, 'update']);
        Route::delete('/{template}', [TemplateController::class, 'destroy']);
        Route::post('/{template}/clone', [TemplateController::class, 'clone']);
    });
});

// Health check
Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'timestamp' => now()->toISOString(),
        'version' => '1.0.0',
    ]);
});
