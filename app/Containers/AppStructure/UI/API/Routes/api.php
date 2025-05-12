<?php

use App\Containers\AppStructure\UI\API\Controllers\NavigateController;
use Illuminate\Support\Facades\Route;


Route::middleware('ensure.browser')->group(function () {
    Route::get('/getNavigation', [NavigateController::class, 'index'])->name('client.main.navigate');
});


