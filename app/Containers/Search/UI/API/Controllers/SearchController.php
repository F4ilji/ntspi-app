<?php

namespace App\Containers\Search\UI\API\Controllers;

use App\Containers\Search\Actions\PerformCrossEloquentSearchAction;
use App\Containers\Search\Tasks\FilterSearchResultsByCategoryTask;
use App\Containers\Search\Tasks\GetAvailableSearchCategoriesTask;
use App\Containers\Search\Tasks\PaginateCollectionTask;
use App\Containers\Search\Tasks\SortAndHighlightSearchResultsTask;
use App\Ship\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $searchQuery = Str::lower($request->query('search'));
        if (!$searchQuery) {
            return response()->json([
                'searchRes' => null,
            ]);
        }

        $allResources = app(PerformCrossEloquentSearchAction::class)->run($searchQuery);
        $result_type = app(GetAvailableSearchCategoriesTask::class)->run($allResources);

        $filteredResources = app(FilterSearchResultsByCategoryTask::class)->run($allResources, $request->query('category'));

        $paginateData = app(PaginateCollectionTask::class)->run($filteredResources, $request, 7);

        $sortedData = app(SortAndHighlightSearchResultsTask::class)->run($paginateData['paginator']->getCollection(), $searchQuery);

        return response()->json([
            'searchRes' => $sortedData,
            'result_type' => $result_type,
            'selectedCategory' => ($request->query('category') !== null) ? $request->query('category') : null,
            'paginate' => [
                'current_page' => $paginateData['paginator']->currentPage(),
                'last_page' => $paginateData['paginator']->lastPage(),
                'total' => $paginateData['paginator']->total(),
                'next_page' => $paginateData['next_page'],
                'prev_page' => $paginateData['prev_page'],
            ]
        ]);
    }
}
