<?php

use App\Containers\Search\UI\API\Controllers\SearchController;
use App\Containers\Search\UI\API\Controllers\StaticSearchController;
use Illuminate\Support\Facades\Route;


Route::get('/search', [SearchController::class, 'index'])->name('client.search.index');

Route::get('/static/search', [StaticSearchController::class, 'search'])->name('client.search.static');
Route::get('/static/categories', [StaticSearchController::class, 'getCategories'])->name('client.categories.static');
