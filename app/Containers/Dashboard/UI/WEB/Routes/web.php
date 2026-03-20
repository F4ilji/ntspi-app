<?php

use App\Containers\Dashboard\UI\WEB\Controllers\IndexDashboardController;
use App\Containers\Dashboard\UI\WEB\Controllers\ProcessMixedFilesController;
use App\Containers\Dashboard\UI\WEB\Controllers\PublishPostController;
use App\Containers\Dashboard\UI\WEB\Controllers\StoreFilesController;
use Illuminate\Support\Facades\Route;


Route::middleware(['access-check', 'superadmin'])->group(function () {
    Route::get('/dashboard', IndexDashboardController::class)->name('dashboard.index');

    // Смешанная загрузка (все файлы в одном поле)
    Route::post('/dashboard/files/store', ProcessMixedFilesController::class)->name('dashboard.files.store');

    // Раздельная загрузка (document + media) - для будущего использования
    Route::post('/dashboard/files/store-separate', StoreFilesController::class)->name('dashboard.files.store-separate');

    // Публикация черновика поста
    Route::post('/dashboard/posts/{post}/publish', PublishPostController::class)->name('dashboard.posts.publish');
});



