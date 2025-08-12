<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome');
});

// Authentication Routes
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [RegisterController::class, 'register']);

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Posts CRUD Routes
Route::resource('posts', PostController::class);

// Test Routes for Queue and Scheduler
Route::prefix('test')->group(function () {
    Route::get('/', [App\Http\Controllers\TestController::class, 'index'])->name('test.index');
    Route::post('/queue', [App\Http\Controllers\TestController::class, 'testQueue'])->name('test.queue');
    Route::post('/scheduler', [App\Http\Controllers\TestController::class, 'testScheduler'])->name('test.scheduler');
    Route::get('/logs', [App\Http\Controllers\TestController::class, 'showLogs'])->name('test.logs');
    Route::get('/queue-status', [App\Http\Controllers\TestController::class, 'queueStatus'])->name('test.queue-status');
});