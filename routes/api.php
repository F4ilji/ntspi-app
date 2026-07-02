<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth:web')->group(function () {
    require __DIR__.'/../Containers/Dashboard/UI/API/Routes/api.php';
});
