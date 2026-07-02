<?php

use Illuminate\Support\Facades\Route;

Route::middleware([
    \Illuminate\Cookie\Middleware\EncryptCookies::class,
    \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
    \Illuminate\Session\Middleware\StartSession::class,
    'auth:web',
])->group(function () {
    require __DIR__.'/../app/Containers/Dashboard/UI/API/Routes/api.php';
});
