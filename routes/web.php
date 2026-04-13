<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HouseholdController;
use App\Http\Controllers\ResponderController;
use App\Http\Controllers\AnalyticController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

// Logout
Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth')->name('logout');

// Auth required routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Household management (static paths before resource so e.g. /households/upload is not matched as {household})
    Route::get('households/upload', [HouseholdController::class, 'uploadForm'])->name('households.upload');
    Route::post('households/upload', [HouseholdController::class, 'upload'])->name('households.upload.process');
    Route::resource('households', HouseholdController::class);
    Route::post('import',[HouseholdController::class,'import']);
    Route::get('export',[HouseholdController::class,'export'])->name('households.export');
    
    // Responders
    Route::resource('responders', ResponderController::class);
    
    // Analytics
    Route::get('analytics', [AnalyticController::class, 'index'])->name('analytics.index');
    
    // Super Admin only
    Route::middleware('is_super_admin')->group(function () {
        Route::resource('roles', RoleController::class);
        Route::resource('permissions', PermissionController::class);
        Route::resource('users', UserController::class);
    });
});

Route::get('/', function () {
    return view('welcome');
});
