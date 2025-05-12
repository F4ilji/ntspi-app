<?php

use App\Containers\Widget\UI\API\Controllers\ClientWidgetAdditionalEducationalProgramController;
use App\Containers\Widget\UI\API\Controllers\ClientWidgetContactController;
use App\Containers\Widget\UI\API\Controllers\ClientWidgetEducationalProgramController;
use App\Containers\Widget\UI\API\Controllers\ClientWidgetFormController;
use App\Containers\Widget\UI\API\Controllers\ClientWidgetPageController;
use App\Containers\Widget\UI\API\Controllers\ClientWidgetPageReferenceListController;
use App\Containers\Widget\UI\API\Controllers\ClientWidgetPostController;
use App\Containers\Widget\UI\API\Controllers\ClientWidgetSliderController;
use Illuminate\Support\Facades\Route;

Route::get('/widget/get-posts', [ClientWidgetPostController::class, 'index'])->name('client.widget.post.index');

Route::get('/widget/get-posts/{id}', [ClientWidgetPostController::class, 'single'])->name('client.widget.post.single');

Route::get('/widget/get-additional-programs', [ClientWidgetAdditionalEducationalProgramController::class, 'index'])->name('client.widget.additional.program.index');

Route::get('/widget/get-educational-programs', [ClientWidgetEducationalProgramController::class, 'index'])->name('client.widget.educational.program.index');

Route::get('/widget/get-page-resource/{id}', [ClientWidgetPageReferenceListController::class, 'show'])->name('client.widget.page.resource.show');

Route::get('/widget/get-contact-widget/{id}', [ClientWidgetContactController::class, 'show'])->name('client.widget.contact.show');

Route::get('/widget/get-page/{path}', [ClientWidgetPageController::class, 'single'])->name('client.widget.page.single');

Route::get('/widget/get-form/{id}', [ClientWidgetFormController::class, 'single'])->middleware('rate.limited.check')->name('client.widget.form.single');

Route::post('/widget/get-form/{id}/submit', [ClientWidgetFormController::class, 'submit'])->middleware(['rate.limited.counter', 'rate.limited.check', 'form.time.period'])->name('client.widget.form.submit');

Route::get('/widget/get-slider/{slug}', [ClientWidgetSliderController::class, 'show'])->name('client.widget.slider.show');