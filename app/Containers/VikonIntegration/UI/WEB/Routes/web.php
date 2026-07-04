<?php

use App\Containers\VikonIntegration\UI\WEB\Controllers\VikonController;
use Illuminate\Support\Facades\Route;

Route::prefix('/dashboard/vikon-updates')
    ->name('dashboard.vikon-updates.')
    ->middleware(['access-check', 'dashboard.auth', 'throttle:30,1', 'vikon.refresh'])
    ->group(function () {
        Route::get('/', [VikonController::class, 'index'])->name('index');
        Route::get('/callback', [VikonController::class, 'oauthCallback'])->name('callback');
        Route::post('/authorize', [VikonController::class, 'getAuthUrl'])->name('authorize');
        Route::post('/authenticate', [VikonController::class, 'authenticate'])->name('authenticate');
        Route::post('/refresh-token', [VikonController::class, 'refreshToken'])->name('refresh-token');
        Route::post('/check-access', [VikonController::class, 'checkAccess'])->name('check-access');
        Route::post('/check-version', [VikonController::class, 'checkVersion'])->name('check-version');
        Route::post('/update-module', [VikonController::class, 'updateModule'])->name('update-module');
        Route::post('/logout', [VikonController::class, 'logout'])->name('logout');
    });
