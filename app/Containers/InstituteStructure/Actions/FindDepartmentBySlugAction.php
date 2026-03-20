<?php

namespace App\Containers\InstituteStructure\Actions;

use App\Containers\InstituteStructure\Models\Department;
use App\Containers\InstituteStructure\Tasks\GetActiveDepartmentsByFacultyIdTask;
use App\Containers\InstituteStructure\Tasks\GetDepartmentBySlugTask;
use App\Containers\InstituteStructure\Tasks\GetFacultyBySlugOnlyTask;
use App\Containers\InstituteStructure\Tasks\GroupProgramsByDirectionTask;
use App\Containers\InstituteStructure\UI\WEB\Transformers\DepartmentResource;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FindDepartmentBySlugAction
{
    public function __construct(
        private readonly GetFacultyBySlugOnlyTask $getFacultyBySlugOnlyTask,
        private readonly GetActiveDepartmentsByFacultyIdTask $getActiveDepartmentsByFacultyIdTask,
        private readonly GetDepartmentBySlugTask $getDepartmentBySlugTask,
        private readonly GroupProgramsByDirectionTask $groupProgramsByDirectionTask
    ) {
    }

    public function run(string $facultySlug, string $departmentSlug): array
    {
        $cacheKey = "{$facultySlug}_{$departmentSlug}";

        $faculty = $this->getFacultyBySlugOnlyTask->run($facultySlug);

        $departments = $this->getActiveDepartmentsByFacultyIdTask->run($faculty->id);

        $departmentModel = $this->getDepartmentBySlugTask->run($departmentSlug, $cacheKey);

        $department = new DepartmentResource($departmentModel);

        $directions = $this->groupProgramsByDirectionTask->run($department->programs);

        return compact('department', 'departments', 'directions', 'departmentModel');
    }
}
