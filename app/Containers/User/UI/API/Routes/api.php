<?php

use App\Services\VK\VkAuthService;
use Illuminate\Support\Facades\Route;


Route::middleware(['web', 'superadmin'])->group(function () {

    Route::get('/login/vk', [VkAuthService::class, 'redirectToProvider'])->name('vk.login');
    Route::get('/login/vk/callback', [VkAuthService::class, 'handleProviderCallback'])->name('vk.callback');
    Route::get('/vk-get-token', [VkAuthService::class, 'getToken'])->name('vk.getToken');
    Route::get('/vk-refresh-token', [VkAuthService::class, 'refresh'])->name('vk.refreshToken');
    Route::get('/vk-logout', [VkAuthService::class, 'logout'])->name('vk.logout');
});