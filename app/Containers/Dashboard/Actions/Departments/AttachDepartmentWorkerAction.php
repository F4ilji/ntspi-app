<?php

namespace App\Containers\Dashboard\Actions\Departments;

use App\Containers\InstituteStructure\Models\Department;
use App\Containers\User\Models\User;
use App\Services\App\Cache\DepartmentCacheService;

class AttachDepartmentWorkerAction
{
    public function __construct(
        private readonly DepartmentCacheService $cacheService,
    ) {}

    public function run(Department $department, User $user, array $data): void
    {
        $department->workers()->attach($user->id, [
            'position' => $data['position'],
            'service_email' => $data['service_email'] ?? null,
            'service_phone' => $data['service_phone'] ?? null,
            'cabinet' => $data['cabinet'] ?? null,
            'sort' => $department->workers()->count() + 1,
        ]);

        $this->cacheService->clearAllCacheByModel();
    }
}
