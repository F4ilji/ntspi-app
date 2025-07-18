<?php

namespace App\Containers\AppStructure\Tasks;

use App\Containers\AppStructure\Models\MainSection;
use App\Containers\AppStructure\UI\API\Transformers\NavigationResource;
use Illuminate\Support\Facades\Cache;

class GetCachedNavigationDataTask
{
    public function run()
    {
        return Cache::remember('navigation', now()->addHours(1), function () {
            return NavigationResource::collection(
                MainSection::with('subSections.pages.section')
                    ->orderBy('sort', 'asc')
                    ->get()
            );
        });
    }
}
