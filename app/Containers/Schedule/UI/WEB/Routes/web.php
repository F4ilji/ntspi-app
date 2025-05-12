<?php

use App\Containers\Schedule\UI\WEB\Controllers\ClientScheduleController;
use Illuminate\Support\Facades\Route;



Route::middleware('access-check')->group(function () {
    // Расписание занятий
    Route::get('/schedule', [ClientScheduleController::class, 'index'])->name('client.schedule.index');
    Route::get('/schedule/{id}', [ClientScheduleController::class, 'show'])->name('client.schedule.show');
});



