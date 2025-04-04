<?php

namespace App\Http\Controllers;

use App\Enums\CacheKeys;
use App\Http\Resources\ClientDepartmentPreviewResource;
use App\Http\Resources\DepartmentResource;
use App\Models\Department;
use App\Models\Faculty;
use App\Services\App\Breadcrumb\BreadcrumbService;
use App\Services\App\Seo\SeoPageProvider;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class ClientDepartmentController extends Controller
{
    public function __construct(readonly SeoPageProvider $seoPageProvider){}

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
                return ClientDepartmentPreviewResource::collection(
                    Department::query()
                        ->where('is_active', true)
                        ->where('faculty_id', $faculty->id)
                        ->get()
                );
            }
        );

        // Кешируем полные данные кафедры с отношениями
        [$department, $seo] = Cache::remember(
            CacheKeys::DEPARTMENT_PREFIX->value . $cacheKey,
            now()->addDay(),
            function () use ($departmentSlug) {
                $department = Department::query()
                    ->where('slug', $departmentSlug)
                    ->where('is_active', true)
                    ->with([
                        'faculty',
                        'workers.userDetail',
                        'teachers.userDetail',
                        'programs.directionStudy',
                        'seo'
                    ])
                    ->first();

                $seo = $this->seoPageProvider->getSeoForModel($department);
                return [
                    new DepartmentResource($department),
                    $seo
                ];
            }
        );

        // Кешируем сгруппированные направления
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


    private function groupProgramsByDirection(Collection $programs)
    {
        // Группируем программы по имени направления
        $grouped = $programs->groupBy(function ($program) {
            return $program->directionStudy->code . " " . $program->directionStudy->name; // Используем имя направления как ключ
        });
        return $grouped;
    }
}
