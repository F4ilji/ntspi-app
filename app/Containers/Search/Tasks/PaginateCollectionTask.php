<?php

namespace App\Containers\Search\Tasks;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class PaginateCollectionTask
{
    public function run(Collection $resources, Request $request, int $perPage = 10): array
    {
        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        // Отрезаем нужные элементы для текущей страницы
        $currentItems = $resources->slice(($currentPage - 1) * $perPage, $perPage)->all();

        // Создаем экземпляр LengthAwarePaginator
        $paginator = new LengthAwarePaginator($currentItems, count($resources), $perPage, $currentPage, [
            'path' => $request->url(),
            'query' => $request->query(), // Changed from $request->query to $request->query()
        ]);

        $nextPage = $paginator->hasMorePages() ? $paginator->currentPage() + 1 : null;
        $prevPage = $paginator->onFirstPage() ? null : $paginator->currentPage() - 1;

        return [
            'paginator' => $paginator,
            'next_page' => $nextPage,
            'prev_page' => $prevPage
        ];
    }
}
