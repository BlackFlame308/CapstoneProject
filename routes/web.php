<?php

use App\Http\Controllers\CentralReportController;

// Existing report routes
Route::get('/reports', [CentralReportController::class, 'index']);
Route::get('/reports/{id}', [CentralReportController::class, 'show']);
Route::post('/reports', [CentralReportController::class, 'store']);

// Token routes
Route::post('/validate-token', [CentralReportController::class, 'validateToken']);
Route::get('/tokens', [CentralReportController::class, 'listTokens']); // optional

Route::get('/', function () {
    return "SafeTrack is running 🚀";
});