<?php

use App\Http\Controllers\CrawlerController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::post('/start-crawler', [CrawlerController::class, 'startCrawler']);