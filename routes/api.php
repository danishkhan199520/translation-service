<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\TranslationController;

Route::prefix('v1')->group(function () {

    // Public routes
    Route::post('auth/login', [AuthController::class, 'login']);
    Route::get('export/{locale}', [TranslationController::class, 'export']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('translations', [TranslationController::class, 'store']);
        Route::put('translations/{key}', [TranslationController::class, 'update']);
        Route::get('translations/{key}', [TranslationController::class, 'show']);
        Route::get('translations', [TranslationController::class, 'index']);
    });
});
