<?php

namespace App\Observers;

use App\Containers\InstituteStructure\Models\Faculty;
use App\Services\App\Cache\FacultyCacheService;

class FacultyObserver
{
    private FacultyCacheService $facultyCacheService;

    public function __construct()
    {
        $this->facultyCacheService = app(FacultyCacheService::class);
    }

    /**
     * Handle the Faculty "created" event.
     */
    public function created(Faculty $faculty): void
    {
        $this->facultyCacheService->clearAllCacheByModel();
    }

    /**
     * Handle the Faculty "updated" event.
     */
    public function updated(Faculty $faculty): void
    {
        $this->facultyCacheService->clearCache($faculty);
        $this->facultyCacheService->clearAllCacheByModel();
    }

    /**
     * Handle the Faculty "deleted" event.
     */
    public function deleted(Faculty $faculty): void
    {
        $this->facultyCacheService->clearCache($faculty);
        $this->facultyCacheService->clearAllCacheByModel();
    }

    /**
     * Handle the Faculty "restored" event.
     */
    public function restored(Faculty $faculty): void
    {
        $this->facultyCacheService->clearAllCacheByModel();
    }

    /**
     * Handle the Faculty "force deleted" event.
     */
    public function forceDeleted(Faculty $faculty): void
    {
        $this->facultyCacheService->clearCache($faculty);
        $this->facultyCacheService->clearAllCacheByModel();
    }
}