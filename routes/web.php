<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HouseholdController;
use App\Http\Controllers\ResponderController;
use App\Http\Controllers\AnalyticController;
use App\Http\Controllers\DisasterEventController;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('households', HouseholdController::class);
Route::resource('responders', ResponderController::class);
Route::get('analytics', [AnalyticController::class, 'index'])->name('analytics.index');
Route::resource('disaster_events', DisasterEventController::class);
