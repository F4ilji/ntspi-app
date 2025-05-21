<?php

use App\Containers\Widget\UI\API\Controllers\ClientWidgetAdditionalEducationalProgramController;
use App\Containers\Widget\UI\API\Controllers\ClientWidgetContactController;
use App\Containers\Widget\UI\API\Controllers\ClientWidgetEducationalProgramController;
use App\Containers\Widget\UI\API\Controllers\ClientWidgetFormController;
use App\Containers\Widget\UI\API\Controllers\ClientWidgetPageController;
use App\Containers\Widget\UI\API\Controllers\ClientWidgetPageReferenceListController;
use App\Containers\Widget\UI\API\Controllers\ClientWidgetPostController;
use App\Containers\Widget\UI\API\Controllers\ClientWidgetSliderController;
use App\Containers\Widget\UI\WEB\Controllers\TvController;
use Illuminate\Support\Facades\Route;

Route::get('/tv/', [TvController::class, 'index'])->name('client.tv.post.index');
