<?php

namespace App\Containers\Dashboard\Actions\Departments;

use App\Containers\Education\Models\EducationalProgram;
use App\Containers\InstituteStructure\Models\Department;
use App\Services\App\Cache\DepartmentCacheService;

class AttachDepartmentProgramAction
{
    public function __construct(
        private readonly DepartmentCacheService $cacheService,
    ) {}

    public function run(Department $department, EducationalProgram $program): void
    {
        $department->programs()->attach($program->id);

        $this->cacheService->clearAllCacheByModel();
    }
}
