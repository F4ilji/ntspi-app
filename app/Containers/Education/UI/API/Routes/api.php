<?php

use App\Containers\Education\UI\API\Controllers\AcademicYearController;
use App\Containers\Education\UI\API\Controllers\UpdateAdmissionCampaignDataApiController;
use App\Containers\Education\UI\API\Controllers\UpdateAdmissionPlansDataApiController;
use App\Containers\Education\UI\API\Controllers\UpdateEduDataApiController;
use Illuminate\Support\Facades\Route;

Route::get('/getAcademicYear', AcademicYearController::class)->name('academic.year');

Route::middleware(['web', 'superadmin'])->group(function () {
    Route::get('/get-edu-program-data', [UpdateEduDataApiController::class, 'index']);
    Route::get('/get-admission-plans-data', [UpdateAdmissionPlansDataApiController::class, 'index']);
    Route::get('/update-admission-campaign-data/{id}', [UpdateAdmissionCampaignDataApiController::class, 'update']);
});
