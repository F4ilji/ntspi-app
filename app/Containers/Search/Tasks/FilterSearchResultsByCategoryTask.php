<?php

namespace App\Containers\Search\Tasks;

use Illuminate\Support\Collection;

class FilterSearchResultsByCategoryTask
{
    public function run(Collection $resources, ?string $category): Collection
    {
        if ($category === null || $category === "All") {
            return $resources;
        } else {
            return $resources->where('type', $category);
        }
    }
}
