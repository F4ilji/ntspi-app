<?php

namespace App\Containers\Education\Tasks;

use App\Containers\Education\Models\EducationalProgram;
use App\Ship\Enums\CacheKeys;
use Illuminate\Support\Facades\Cache;

class GetEducationalProgramBySlugTask
{
    public function run(string $slug)
    {
        $cacheKeyProgram = CacheKeys::EDUCATION_PROGRAM_PREFIX->value . md5($slug);

        return Cache::remember($cacheKeyProgram, now()->addHours(1), function () use ($slug) {
            return EducationalProgram::query()
                ->where('slug', $slug)
                ->with(['admission_plans', 'directionStudy', 'seo'])
                ->firstOrFail();
        });
    }
}
