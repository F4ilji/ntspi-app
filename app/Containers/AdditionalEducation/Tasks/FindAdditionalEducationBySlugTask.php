<?php

namespace App\Containers\AdditionalEducation\Tasks;

use App\Containers\AdditionalEducation\Models\AdditionalEducation;
use App\Ship\Enums\CacheKeys;
use App\Ship\Tasks\Task;
use Illuminate\Support\Facades\Cache;

class FindAdditionalEducationBySlugTask
{
    public function run(string $slug)
    {
        return Cache::remember(
            CacheKeys::ADDITIONAL_EDUCATIONAL_PROGRAM_PREFIX->value . $slug,
            now()->addDay(),
            fn() => AdditionalEducation::with('category.direction')->where('slug', $slug)->firstOrFail()
        );
    }
}
