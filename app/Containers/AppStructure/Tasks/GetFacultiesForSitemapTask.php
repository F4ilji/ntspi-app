<?php

namespace App\Containers\AppStructure\Tasks;

use App\Containers\InstituteStructure\Models\Faculty;

class GetFacultiesForSitemapTask
{
    public function run()
    {
        return Faculty::query()
            ->where('is_active', true)
            ->get();
    }
}
