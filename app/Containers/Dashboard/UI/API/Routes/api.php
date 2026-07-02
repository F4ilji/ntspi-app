<?php

use App\Containers\Dashboard\UI\API\Controllers\DeployController;
use Illuminate\Support\Facades\Route;

Route::middleware([
    \App\Ship\Middleware\EncryptCookies::class,
    \Illuminate\Session\Middleware\StartSession::class,
    \App\Ship\Middleware\VerifyCsrfToken::class,
    \Illuminate\Routing\Middleware\SubstituteBindings::class,
    'superadmin',
])->group(function () {
    Route::post('/deploy', [DeployController::class, 'store']);
    Route::get('/deploy/status', [DeployController::class, 'status']);
});
