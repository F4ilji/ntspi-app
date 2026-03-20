<?php

use App\Containers\Science\UI\WEB\Controllers\ClientAcademicJournalController;
use Illuminate\Support\Facades\Route;


Route::middleware('access-check')->group(function () {
    Route::get('/academic-journals/', [ClientAcademicJournalController::class, 'index'])->name('client.academicJournals.index');
    Route::get('/academic-journals/{slug}', [ClientAcademicJournalController::class, 'show'])->name('client.academicJournals.show');
});


