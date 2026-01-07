<?php

use App\Http\Controllers\Api\ImportController;
use App\Http\Controllers\Api\InvestorController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Health check
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toIso8601String(),
    ]);
});

// CSV Import
Route::post('/import', [ImportController::class, 'import']);

// Investor endpoints
Route::prefix('investors')->group(function () {
    // Aggregate endpoints
    Route::get('/average-age', [InvestorController::class, 'averageAge']);
    Route::get('/average-investment', [InvestorController::class, 'averageInvestment']);
    Route::get('/total-investments', [InvestorController::class, 'totalInvestments']);
    
    // List all investors
    Route::get('/', [InvestorController::class, 'index']);
});