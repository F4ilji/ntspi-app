<?php

namespace App\Containers\Search\Tasks;

use Illuminate\Support\Collection;

class GetAvailableSearchCategoriesTask
{
    public function run(Collection $resources): array
    {
        return $resources->pluck('type')->unique()->values()->all();
    }
}
