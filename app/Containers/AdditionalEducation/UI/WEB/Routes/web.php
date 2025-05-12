<?php

use App\Containers\AdditionalEducation\UI\WEB\Controllers\ClientAdditionalEducationController;
use Illuminate\Support\Facades\Route;


Route::middleware('access-check')->group(function () {
    Route::get('/additional-education/{slug}', [ClientAdditionalEducationController::class, 'show'])->name('client.additionalEducation.show');
    Route::get('/additional-education/', [ClientAdditionalEducationController::class, 'index'])->name('client.additionalEducation.index');
});


