<?php

namespace App\Containers\AppStructure\Tasks;

use App\Containers\AppStructure\Models\MainSection;

class GetNavigationDataTask
{
    public function run()
    {
        return MainSection::with('subSections.pages')
            ->orderBy('sort', 'asc')
            ->get();
    }
}
