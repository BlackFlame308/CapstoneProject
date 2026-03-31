<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\HouseholdController;
use App\Http\Controllers\API\MemberController;
use App\Http\Controllers\API\AnalyticController;
use App\Http\Controllers\API\ResponderController;
use App\Http\Controllers\API\EvacuationOfficerController;
use App\Http\Controllers\API\ReportController;

// Public routes
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// Protected routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('change-password', [AuthController::class, 'changePassword']);

    Route::get('user', function (Request $request) {
        return response()->json(["status" => "success", "data" => $request->user()]);
    });

    // Household Management
    Route::apiResource('households', HouseholdController::class);
    Route::post('households/upload-csv', [HouseholdController::class, 'uploadCsv']);

    // Member Management
    Route::apiResource('members', MemberController::class);

    // Responders
    Route::apiResource('responders', ResponderController::class);

    // Evacuation Officers
    Route::apiResource('evacuation-officers', EvacuationOfficerController::class);

    // Reports
    Route::apiResource('reports', ReportController::class);

    // Analytics
    Route::get('analytics/barangay', [AnalyticController::class, 'barangay']);
    Route::get('analytics/sitio', [AnalyticController::class, 'sitio']);
    Route::post('analytics/refresh', [AnalyticController::class, 'refresh']);
});
