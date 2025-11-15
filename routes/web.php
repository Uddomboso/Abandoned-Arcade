<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Web\GameController;
use App\Http\Controllers\Web\PageController;
use App\Http\Controllers\Web\ProfileController;
use App\Http\Controllers\Web\WorkOSAuthController;
use Illuminate\Support\Facades\Route;

// public routes
// these routes are accessible without authentication
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/games', [GameController::class, 'index'])->name('games.index');
Route::get('/games/{id}', [GameController::class, 'show'])->name('games.show');
Route::get('/games/{id}/play', [GameController::class, 'play'])->name('games.play');

// authentication routes
// handles user login and registration
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// workos authentication routes
// handles oauth login via workos service
Route::get('/auth/workos', [WorkOSAuthController::class, 'redirectToWorkOS'])->name('workos.login');
Route::get('/auth/workos/callback', [WorkOSAuthController::class, 'handleCallback'])->name('workos.callback');
Route::post('/auth/logout', [WorkOSAuthController::class, 'logout'])->name('logout');

// protected routes (require authentication)
// these routes require user to be logged in
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/collection', [ProfileController::class, 'collection'])->name('profile.collection');
    Route::post('/reviews', [\App\Http\Controllers\Web\ReviewController::class, 'store'])->name('reviews.store');
});
