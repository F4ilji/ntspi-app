<?php

use App\Containers\VikonIntegration\UI\WEB\Controllers\VikonUpdateController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Vikon Update Management Routes
|--------------------------------------------------------------------------
|
| Secure routes for Vikon module update management.
| All routes are protected by:
| - access-check: Verify route is registered in DB
| - dashboard.auth: Require authentication
| - throttle: Rate limiting (30 requests per minute)
|
| Replaces old: /vikon_core/update/*.php direct access
|
*/

Route::prefix('/dashboard/vikon-updates')
    ->name('dashboard.vikon-updates.')
    ->middleware(['access-check', 'dashboard.auth', 'throttle:30,1'])
    ->group(function () {
        // Main page
        Route::get('/', [VikonUpdateController::class, 'index'])->name('index');

        // OAuth authentication
        Route::post('/authenticate', [VikonUpdateController::class, 'authenticate'])->name('authenticate');
        Route::post('/refresh-token', [VikonUpdateController::class, 'refreshToken'])->name('refresh-token');
        Route::post('/logout', [VikonUpdateController::class, 'logout'])->name('logout');

        // Access & version checks
        Route::post('/check-access', [VikonUpdateController::class, 'checkAccess'])->name('check-access');
        Route::post('/check-version', [VikonUpdateController::class, 'checkVersion'])->name('check-version');
        Route::get('/check-entry', [VikonUpdateController::class, 'checkEntry'])->name('check-entry');

        // Update operations
        Route::post('/download-update', [VikonUpdateController::class, 'downloadUpdate'])->name('download-update');
        Route::post('/sync-files', [VikonUpdateController::class, 'syncFiles'])->name('sync-files');
    });
