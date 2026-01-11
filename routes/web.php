<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ScreenshotController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', function () {
    return redirect('/login');
});

// Authentication
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');

// Dashboard routes (authenticated users)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/pages/{page}', [DashboardController::class, 'show'])->name('dashboard.page');
    Route::get('/api/pages/{page}/metrics', [DashboardController::class, 'pageMetrics']);
    
    // Screenshot proxy (serves private bucket files through authenticated controller)
    Route::get('/screenshots/{bundleSize}/{filename}', [ScreenshotController::class, 'show'])
        ->name('screenshots.show')
        ->where('filename', '.*');
});
