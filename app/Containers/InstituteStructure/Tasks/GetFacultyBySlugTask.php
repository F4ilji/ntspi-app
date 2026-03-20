<?php

namespace App\Containers\InstituteStructure\Tasks;

use App\Containers\InstituteStructure\Models\Faculty;
use App\Ship\Enums\CacheKeys;
use Illuminate\Support\Facades\Cache;

class GetFacultyBySlugTask
{
    public function run(string $slug): Faculty
    {
        return Cache::remember(
            CacheKeys::FACULTY_PREFIX->value . $slug,
            now()->addDay(),
            function () use ($slug) {
                return Faculty::where('slug', $slug)
                    ->where('is_active', true)
                    ->with(['departments.faculty',
                        'workers' => fn ($query) => $query->orderBy('sort', 'asc'),
                        'workers.userDetail',
                        'seo'
                    ])
                    ->firstOrFail();
            }
        );
    }
}
