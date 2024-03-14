<?php

use App\Http\Controllers\ShortUrlController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Index');
});

Route::resource('/short-urls', ShortUrlController::class)->only(['index', 'store']);
Route::get('/short-urls/{shortUrl}', [ShortUrlController::class, 'show']);
