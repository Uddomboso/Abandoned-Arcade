<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GameController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\SaveController;
use Illuminate\Support\Facades\Route;

// api routes
// provides json api endpoints for frontend applications
// all routes are prefixed with /api

// public routes
// accessible without authentication
Route::get('/games', [GameController::class, 'index']);
Route::get('/games/{id}', [GameController::class, 'show']);
Route::get('/reviews', [ReviewController::class, 'index']);
Route::get('/reviews/{id}', [ReviewController::class, 'show']);

// authentication routes
// handles user registration and login
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// protected routes (require sanctum token authentication)
// used by mobile apps or spa applications
Route::middleware('auth:sanctum')->group(function () {
    // auth endpoints
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // guest data sync
    // allows syncing localstorage data when user logs in
    Route::post('/guest/sync', [\App\Http\Controllers\Api\GuestController::class, 'sync']);
});

// web session based routes (for web auth)
// uses session authentication instead of token
Route::middleware('auth')->group(function () {
    // get current authenticated user
    Route::get('/user', function () {
        return response()->json(['user' => auth()->user()]);
    });
    // guest data sync for web users
    Route::post('/guest/sync', [\App\Http\Controllers\Api\GuestController::class, 'sync']);

    // games crud operations
    // create, update, delete games (admin only in production)
    Route::post('/games', [GameController::class, 'store']);
    Route::put('/games/{id}', [GameController::class, 'update']);
    Route::delete('/games/{id}', [GameController::class, 'destroy']);

    // reviews crud operations
    // users can create, update, and delete their own reviews
    Route::post('/reviews', [ReviewController::class, 'store']);
    Route::put('/reviews/{id}', [ReviewController::class, 'update']);
    Route::delete('/reviews/{id}', [ReviewController::class, 'destroy']);

    // save states crud operations
    // manage game save states for users
    Route::get('/saves', [SaveController::class, 'index']);
    Route::post('/saves', [SaveController::class, 'store']);
    Route::get('/saves/{id}', [SaveController::class, 'show']);
    Route::put('/saves/{id}', [SaveController::class, 'update']);
    Route::delete('/saves/{id}', [SaveController::class, 'destroy']);
});
