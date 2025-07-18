<?php

namespace App\Containers\Search\UI\API\Controllers;

use App\Containers\Search\Actions\SearchStaticFilesAction;
use App\Containers\Search\Tasks\GetStaticFileCategoriesTask;
use App\Ship\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class StaticSearchController extends Controller
{
    public function search(Request $request)
    {
        return app(SearchStaticFilesAction::class)
            ->run($request);
    }

    public function getCategories()
    {
        return Cache::remember('page_static_categories', now()->addWeek(), function () {
            return app(GetStaticFileCategoriesTask::class)->run();
        });
    }
}

