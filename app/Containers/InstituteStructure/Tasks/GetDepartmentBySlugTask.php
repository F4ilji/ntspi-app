<?php

namespace App\Containers\InstituteStructure\Tasks;

use App\Containers\InstituteStructure\Models\Department;
use App\Ship\Enums\CacheKeys;
use Illuminate\Support\Facades\Cache;

class GetDepartmentBySlugTask
{
    public function run(string $departmentSlug, string $cacheKey): Department
    {
        return Cache::remember(
            CacheKeys::DEPARTMENT_PREFIX->value . $cacheKey,
            now()->addDay(),
            function () use ($departmentSlug) {
                return Department::query()
                    ->where('slug', $departmentSlug)
                    ->where('is_active', true)
                    ->with([
                        'faculty',
                        'workers' => fn ($query) => $query->orderBy('sort', 'asc'),
                        'teachers' => fn ($query) => $query->orderBy('sort', 'asc'),
                        'programs' => fn ($query) => $query->orderBy('sort', 'asc'),
                        'workers.userDetail',
                        'teachers.userDetail',
                        'programs.directionStudy',
                        'seo'
                    ])
                    ->firstOrFail();
            }
        );
    }
}
