<?php

namespace App\Containers\InstituteStructure\Tasks;

use App\Containers\InstituteStructure\Models\Department;
use App\Containers\InstituteStructure\UI\WEB\Transformers\DepartmentPreviewResource;
use App\Ship\Enums\CacheKeys;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;

class GetActiveDepartmentsByFacultyIdTask
{
    public function run(int $facultyId): AnonymousResourceCollection
    {
        return Cache::remember(
            CacheKeys::DEPARTMENTS_PREFIX->value . 'active_' . $facultyId,
            now()->addDay(),
            function () use ($facultyId) {
                return DepartmentPreviewResource::collection(
                    Department::query()
                        ->where('is_active', true)
                        ->where('faculty_id', $facultyId)
                        ->get()
                );
            }
        );
    }
}
