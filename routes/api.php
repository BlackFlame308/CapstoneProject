<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\HouseholdController;
use App\Http\Controllers\API\MemberController;
use App\Http\Controllers\API\AnalyticController;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);

    Route::get('user', function (Request $request) {
        return response()->json(["status" => "success", "data" => $request->user()]);
    });

    Route::apiResource('households', HouseholdController::class);
    Route::post('households/upload-csv', [HouseholdController::class, 'uploadCsv']);

    Route::apiResource('members', MemberController::class);

    Route::get('analytics/barangay', [AnalyticController::class, 'barangay']);
    Route::get('analytics/sitio', [AnalyticController::class, 'sitio']);
    Route::post('analytics/refresh', [AnalyticController::class, 'refresh']);
});
