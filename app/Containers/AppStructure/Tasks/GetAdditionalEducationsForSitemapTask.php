<?php

namespace App\Containers\AppStructure\Tasks;

use App\Containers\AdditionalEducation\Models\AdditionalEducation;

class GetAdditionalEducationsForSitemapTask
{
    public function run()
    {
        return AdditionalEducation::query()
            ->where('is_active', true)
            ->get();
    }
}
