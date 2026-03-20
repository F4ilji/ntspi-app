<?php

use App\Containers\AppStructure\UI\WEB\Controllers\PageController;
use App\Containers\Main\UI\WEB\Controllers\MainController;
use Illuminate\Support\Facades\Route;

Route::middleware('access-check')->group(function () {

    Route::get('/', [MainController::class, 'index'])->name('index');

    Route::get('{path}', [PageController::class, 'render'])->where('path', '[0-9,a-z,/,-]+')->name('page.view');
});


