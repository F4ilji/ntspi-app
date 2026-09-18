<?php

use App\Containers\Analytics\UI\WEB\Controllers\TrackController;
use Illuminate\Support\Facades\Route;

Route::post('/track/hit', [TrackController::class, 'store'])
    ->middleware(['throttle:60,1'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
