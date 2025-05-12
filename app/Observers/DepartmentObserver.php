<?php

namespace App\Observers;

use App\Containers\InstituteStructure\Models\Department;
use App\Services\App\Cache\DepartmentCacheService;

class DepartmentObserver
{
    private DepartmentCacheService $departmentCacheService;

    public function __construct()
    {
        $this->departmentCacheService = app(DepartmentCacheService::class);
    }

    /**
     * Handle the Department "created" event.
     */
    public function created(Department $department): void
    {
        $this->departmentCacheService->clearAllCacheByModel();
    }

    /**
     * Handle the Department "updated" event.
     */
    public function updated(Department $department): void
    {
        $this->departmentCacheService->clearCache($department);
        $this->departmentCacheService->clearAllCacheByModel(); // Очищаем и общий кэш, если есть списки
    }

    /**
     * Handle the Department "deleted" event.
     */
    public function deleted(Department $department): void
    {
        $this->departmentCacheService->clearCache($department);
        $this->departmentCacheService->clearAllCacheByModel();
    }

    /**
     * Handle the Department "restored" event.
     */
    public function restored(Department $department): void
    {
        $this->departmentCacheService->clearAllCacheByModel();
    }

    /**
     * Handle the Department "force deleted" event.
     */
    public function forceDeleted(Department $department): void
    {
        $this->departmentCacheService->clearCache($department);
        $this->departmentCacheService->clearAllCacheByModel();
    }
}