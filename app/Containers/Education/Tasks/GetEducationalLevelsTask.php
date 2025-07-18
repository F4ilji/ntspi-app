<?php

namespace App\Containers\Education\Tasks;

use App\Containers\Education\Models\EducationalProgram;
use Illuminate\Support\Facades\Cache;

class GetEducationalLevelsTask
{
    public function run()
    {
        $cacheKeyLevels = 'education_levels_list';
        return Cache::remember($cacheKeyLevels, now()->addDay(), function () {
            return EducationalProgram::distinct()->pluck('lvl_edu')
                ->mapWithKeys(fn($level) => [$level->name => $level->getLabel()]);
        });
    }
}
