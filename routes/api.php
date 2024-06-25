<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\DashboardController;

Route::middleware('auth:sanctum')->group(function () {
    /* Default User Route */
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    /* Logout Route */
    Route::post('/logout', [AuthController::class, 'logout']);

    /* Survey Resource Route */
    Route::resource('/survey', SurveyController::class);

    /* Dashboard Route */
    Route::get('/dashboard', [DashboardController::class, 'index']);
});

/* Survey Public Answers Routes */
Route::get('/survey-by-slug/{survey:slug}', [SurveyController::class, 'showForGuest']);
Route::post('/survey/{survey}/answer', [SurveyController::class, 'storeAnswer']);

/* Auth Routes */
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
