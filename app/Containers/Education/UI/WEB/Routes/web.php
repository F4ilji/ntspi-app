<?php

use App\Containers\Education\UI\WEB\Controllers\ClientProgramController;
use Illuminate\Support\Facades\Route;


Route::middleware('access-check')->group(function () {
    Route::get('/programs/', [ClientProgramController::class, 'index'])->name('client.program.index');
    Route::get('/program/{slug}', [ClientProgramController::class, 'show'])->name('client.program.show');
});


