<?php

use App\Http\Controllers\CrawlerController;
use Illuminate\Support\Facades\Route;

// API routes that accept either Sanctum tokens or API keys
Route::middleware(['api.key'])->group(function () {
    Route::post('/crawler', [CrawlerController::class, 'startCrawler'])->name('api.crawler');
});

// API routes that require Sanctum authentication (for token-based access)
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/crawler/token', [CrawlerController::class, 'startCrawler'])->name('api.crawler.token');
});

