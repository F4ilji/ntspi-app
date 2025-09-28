<?php


use App\Containers\Widget\UI\WEB\Controllers\TvController;
use Illuminate\Support\Facades\Route;

Route::get('/tv/', [TvController::class, 'index'])->name('client.tv.post.index');
Route::get('/tv/time', [TvController::class, 'time'])->name('client.tv.post.time');

