<?php

namespace App\Containers\Search\Tasks;

class PaginateSearchResultsTask
{
    public function run(array $results, int $page, int $perPage, ?array $categories): array
    {
        $total = count($results);
        $lastPage = max(1, ceil($total / $perPage));
        $page = max(1, min($page, $lastPage));

        $offset = ($page - 1) * $perPage;
        $paginatedResults = array_slice($results, $offset, $perPage);

        return [
            'data' => $paginatedResults,
            'meta' => [
                'current_page' => $page,
                'total' => $total,
                'per_page' => $perPage,
                'last_page' => $lastPage
            ],
            'categories' => $categories
        ];
    }
}
