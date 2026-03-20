<?php

namespace App\Containers\InstituteStructure\Tasks;

use App\Containers\InstituteStructure\Models\Faculty;
use App\Ship\Enums\CacheKeys;
use Illuminate\Support\Facades\Cache;

class GetFacultyBySlugOnlyTask
{
    public function run(string $facultySlug): Faculty
    {
        return Cache::remember(
            CacheKeys::FACULTY_PREFIX->value . $facultySlug,
            now()->addDay(),
            function () use ($facultySlug) {
                return Faculty::query()
                    ->where('slug', $facultySlug)
                    ->firstOrFail();
            }
        );
    }
}
