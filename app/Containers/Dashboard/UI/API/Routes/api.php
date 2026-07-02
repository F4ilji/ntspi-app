<?php

use App\Containers\Dashboard\UI\API\Controllers\DeployController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'superadmin'])->group(function () {
    Route::post('/deploy', [DeployController::class, 'store']);
    Route::get('/deploy/status', [DeployController::class, 'status']);
});
