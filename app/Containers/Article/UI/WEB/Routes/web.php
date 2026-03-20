<?php

use App\Containers\Article\UI\WEB\Controllers\IndexPostController;
use App\Containers\Article\UI\WEB\Controllers\ShowPostController;
use Illuminate\Support\Facades\Route;


Route::middleware('access-check')->group(function () {
    Route::get('/news', IndexPostController::class)->name('client.post.index');
    Route::get('/news/{slug}', ShowPostController::class)->name('client.post.show');
});



