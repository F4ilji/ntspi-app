<?php

use App\Containers\Event\UI\WEB\Controllers\ClientEventController;
use Illuminate\Support\Facades\Route;


Route::middleware('access-check')->group(function () {
    Route::get('/events', [ClientEventController::class, 'index'])->name('client.event.index');
    Route::get('/events/archive', [ClientEventController::class, 'archive'])->name('client.event.archive'); // Доделать builder
    Route::get('/events/{slug}', [ClientEventController::class, 'show'])->name('client.event.show');
});


