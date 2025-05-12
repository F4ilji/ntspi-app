<?php

namespace App\Containers\InstituteStructure\UI\WEB\Controllers;

use App\Containers\InstituteStructure\Models\Department;
use App\Containers\InstituteStructure\Models\Faculty;
use App\Containers\InstituteStructure\UI\WEB\Transformers\DepartmentPreviewResource;
use App\Containers\InstituteStructure\UI\WEB\Transformers\DepartmentResource;
use App\Ship\Contracts\SeoServiceInterface;
use App\Ship\Controllers\Controller;
use App\Ship\Enums\CacheKeys;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class ClientDepartmentController extends Controller
{
    public function __construct(readonly SeoServiceInterface $seoPageProvider){}

    public function show(string $facultySlug, string $departmentSlug)
    {
        // Ключ для кеширования
        $cacheKey = "{$facultySlug}_{$departmentSlug}";

        // Кешируем факультет
        $faculty = Cache::remember(
            CacheKeys::FACULTY_PREFIX->value . $facultySlug,
            now()->addDay(),
            function () use ($facultySlug) {
                return Faculty::query()
                    ->where('slug', $facultySlug)
                    ->first();
            }
        );



        // Кешируем список активных кафедр факультета
        $departments = Cache::remember(
            CacheKeys::DEPARTMENTS_PREFIX->value . 'active_' . $faculty->id,
            now()->addDay(),
            function () use ($faculty) {
                return DepartmentPreviewResource::collection(
                    Department::query()
                        ->where('is_active', true)
                        ->where('faculty_id', $faculty->id)
                        ->get()
                );
            }
        );

        $departmentModel = Cache::remember(
            CacheKeys::DEPARTMENT_PREFIX->value . $cacheKey,
            now()->addDay(),
            function () use ($departmentSlug) {
                return Department::query()
                    ->where('slug', $departmentSlug)
                    ->where('is_active', true)
                    ->with([
                        'faculty',
                        'workers.userDetail',
                        'teachers.userDetail',
                        'programs.directionStudy',
                        'seo'
                    ])
                    ->firstOrFail();
            }
        );

        $seo = Cache::remember(
            CacheKeys::DEPARTMENT_PREFIX->value . 'seo_' . $cacheKey,
            now()->addDay(),
            function () use ($departmentModel) {
                return $this->seoPageProvider->getSeoForModel($departmentModel);
            }
        );

        $department = new DepartmentResource($departmentModel);

        $directions = Cache::remember(
            CacheKeys::DEPARTMENT_PREFIX->value . 'directions_' . $cacheKey,
            now()->addDay(),
            function () use ($department) {
                return $this->groupProgramsByDirection($department->programs);
            }
        );


        return Inertia::render('Client/Departments/Show', compact(
            'department',
            'departments',
            'directions',
            'seo',
        ));
    }


    private function groupProgramsByDirection(Collection $programs): Collection
    {
        // Группируем программы по имени направления
        return $programs->groupBy(function ($program) {
            return $program->directionStudy->code . " " . $program->directionStudy->name; // Используем имя направления как ключ
        });
    }
}
