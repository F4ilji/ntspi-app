<?php

namespace App\Containers\AppStructure\Tasks;

use App\Containers\InstituteStructure\Models\Division;

class GetDivisionsForSitemapTask
{
    public function run()
    {
        return Division::query()
            ->where('is_active', true)
            ->get();
    }
}
