<?php

use App\Containers\InstituteStructure\UI\WEB\Controllers\ClientDepartmentController;
use App\Containers\InstituteStructure\UI\WEB\Controllers\ClientDivisionController;
use App\Containers\InstituteStructure\UI\WEB\Controllers\ClientFacultyController;
use Illuminate\Support\Facades\Route;



Route::middleware('access-check')->group(function () {
    // Факультеты и кафедры
    Route::get('/faculties', [ClientFacultyController::class, 'index'])->name('client.faculty.index');
    Route::get('/faculties/{slug}', [ClientFacultyController::class, 'show'])->name('client.faculty.show');
    Route::get('/faculties/{facultySlug}/{departmentSlug}', [ClientDepartmentController::class, 'show'])->name('client.department.show');

    // Подразделения института
    Route::get('/divisions', [ClientDivisionController::class, 'index'])->name('client.division.index');
    Route::get('/divisions/{slug}', [ClientDivisionController::class, 'show'])->name('client.division.show');
});


