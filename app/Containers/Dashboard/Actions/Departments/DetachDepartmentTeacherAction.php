<?php

namespace App\Containers\Dashboard\Actions\Departments;

use App\Containers\InstituteStructure\Models\Department;
use App\Containers\User\Models\User;
use App\Services\App\Cache\DepartmentCacheService;

class DetachDepartmentTeacherAction
{
    public function __construct(
        private readonly DepartmentCacheService $cacheService,
    ) {}

    public function run(Department $department, User $user): void
    {
        $department->teachers()->detach($user->id);

        $this->cacheService->clearAllCacheByModel();
    }
}
