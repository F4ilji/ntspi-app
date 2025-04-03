<?php

namespace App\Http\Controllers;

use App\Services\Filament\Services\CategoryFinderService;
use App\Services\Filament\Services\StaticFileSearch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class StaticSearchController extends Controller
{
    public function search(Request $request)
    {
        return app(StaticFileSearch::class)
            ->search($request);
    }

    public function getCategories()
    {
        return Cache::remember('page_static_categories', now()->addWeek(), function () {
            return app(CategoryFinderService::class)->getCategories();
        });
    }
}

