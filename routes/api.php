<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth:web'])->group(function () {
    require __DIR__.'/../app/Containers/Dashboard/UI/API/Routes/api.php';
});
