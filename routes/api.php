<?php

use App\Http\Controllers\api\NavigateController;
use App\Http\Controllers\ClientWidgetAdditionalEducationalProgramController;
use App\Http\Controllers\ClientWidgetEducationalProgramController;
use App\Http\Controllers\ClientWidgetFormController;
use App\Http\Controllers\ClientWidgetPageController;
use App\Http\Controllers\ClientWidgetPageReferenceListController;
use App\Http\Controllers\ClientWidgetPostController;
use App\Http\Controllers\IconController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\VkAuthController;
use App\Http\Controllers\VkPostController;
use App\Models\AdmissionCampaign;
use App\Services\Vicon\DirectionStudy\DirectionStudyService;
use App\Services\VK\VkAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/getNavigation', [NavigateController::class, 'index'])->name('client.main.navigate');

Route::get('/getAcademicYear', function () {
    $activeCampaign = AdmissionCampaign::query()->where('status', 1)->first();
    return $activeCampaign->academic_year;
})->name('academic.year');

Route::get('/test', function () {
    $service = new DirectionStudyService();
    dd($service->getAllNaprsUuid());
});

Route::get('/search', [SearchController::class, 'index'])->name('client.search.index');

Route::get('/widget/get-posts', [ClientWidgetPostController::class, 'index'])->name('client.widget.post.index');

Route::get('/widget/get-posts/{id}', [ClientWidgetPostController::class, 'single'])->name('client.widget.post.single');

Route::get('/widget/get-additional-programs', [ClientWidgetAdditionalEducationalProgramController::class, 'index'])->name('client.widget.additional.program.index');

Route::get('/widget/get-educational-programs', [ClientWidgetEducationalProgramController::class, 'index'])->name('client.widget.educational.program.index');


Route::get('/widget/get-page-resource/{id}', [ClientWidgetPageReferenceListController::class, 'show'])->name('client.widget.page.resource.index');


Route::get('/widget/get-page/{id}', [ClientWidgetPageController::class, 'single'])->name('client.widget.page.single');

Route::get('/widget/get-form/{id}', [ClientWidgetFormController::class, 'single'])->middleware('rate.limited.check')->name('client.widget.form.single');

Route::post('/widget/get-form/{id}/submit', [ClientWidgetFormController::class, 'submit'])->middleware(['rate.limited.counter', 'rate.limited.check'])->name('client.widget.form.submit');


Route::get('/login/vk', [VkAuthService::class, 'redirectToProvider'])->name('vk.login');
Route::get('/login/vk/callback', [VkAuthService::class, 'handleProviderCallback'])->name('vk.callback');
Route::get('/vk-get-token', [VkAuthService::class, 'getToken'])->name('vk.getToken');
Route::get('/vk-refresh-token', [VkAuthService::class, 'refresh'])->name('vk.refreshToken');
Route::get('/vk-logout', [VkAuthService::class, 'logout'])->name('vk.logout');



//Route::get('/vk-handle', [VkPostController::class, 'index']);
//Route::get('/vk-handle/get-auth-token', [VkAuthController::class, 'getToken']);
//Route::get('/vk-handle/wall', [VkPostController::class, 'wall']);