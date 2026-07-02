<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['access-check', 'dashboard.auth'])->group(function () {
    require __DIR__.'/../app/Containers/Dashboard/UI/API/Routes/api.php';
});
