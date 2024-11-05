<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClientDepartmentPreviewResource;
use App\Http\Resources\DepartmentResource;
use App\Models\Department;
use App\Models\Faculty;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClientDepartmentController extends Controller
{
    public function show(string $facultySlug, string $departmentSlug)
    {
        $faculty = Faculty::query()->where('slug', $facultySlug)->first();
        $departments = ClientDepartmentPreviewResource::collection(
            Department::query()
                ->where('is_active', true)
                ->where('faculty_id', $faculty->id)
                ->get()
        );
        $department = new DepartmentResource(Department::query()
            ->where('slug', $departmentSlug)
            ->where('is_active', true)
            ->with(['faculty', 'workers.userDetail', 'teachers.userDetail', 'programs.directionStudy'])
            ->first());
        $directions = $this->groupProgramsByDirection($department->programs);

        $seo = $department->seo;
        return Inertia::render('Client/Departments/Show', compact('department', 'departments', 'directions', 'seo'));
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
