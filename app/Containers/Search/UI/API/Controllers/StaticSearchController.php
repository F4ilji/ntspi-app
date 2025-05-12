<?php

namespace App\Containers\Search\UI\API\Controllers;

use App\Containers\Search\Services\CategoryFinderService;
use App\Containers\Search\Services\StaticFileSearch;
use App\Ship\Controllers\Controller;
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

