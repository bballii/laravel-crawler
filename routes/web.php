<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CrawlerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ApiKeyController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CrawlerHistoryController;
use Illuminate\Support\Facades\Route;

// Authentication routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Redirect root to dashboard if authenticated, otherwise to login
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Protected dashboard routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Crawler routes
    Route::get('/crawler', [DashboardController::class, 'crawler'])->name('crawler');
    Route::post('/start-crawler', [CrawlerController::class, 'startCrawler'])->name('crawler.start');
    
    // API Keys management
    Route::resource('api-keys', ApiKeyController::class);
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    
    // Crawler History (Super Admin only)
    Route::middleware(['superadmin'])->group(function () {
        Route::get('/crawler-history', [CrawlerHistoryController::class, 'index'])->name('crawler-history.index');
    });
});