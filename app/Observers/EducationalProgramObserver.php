<?php

namespace App\Observers;

use App\Models\EducationalProgram;
use App\Services\App\Cache\EducationalProgramCacheService;

class EducationalProgramObserver
{
    private EducationalProgramCacheService $educationalProgramCacheService;

    public function __construct()
    {
        $this->educationalProgramCacheService = app(EducationalProgramCacheService::class);
    }

    /**
     * Handle the EducationalProgram "created" event.
     */
    public function created(EducationalProgram $educationalProgram): void
    {
        $this->educationalProgramCacheService->clearAllCacheByModel();
    }

    /**
     * Handle the EducationalProgram "updated" event.
     */
    public function updated(EducationalProgram $educationalProgram): void
    {
        $this->educationalProgramCacheService->clearCache($educationalProgram);
        $this->educationalProgramCacheService->clearAllCacheByModel();
    }

    /**
     * Handle the EducationalProgram "deleted" event.
     */
    public function deleted(EducationalProgram $educationalProgram): void
    {
        $this->educationalProgramCacheService->clearCache($educationalProgram);
        $this->educationalProgramCacheService->clearAllCacheByModel();
    }

    /**
     * Handle the EducationalProgram "restored" event.
     */
    public function restored(EducationalProgram $educationalProgram): void
    {
        $this->educationalProgramCacheService->clearAllCacheByModel();
    }

    /**
     * Handle the EducationalProgram "force deleted" event.
     */
    public function forceDeleted(EducationalProgram $educationalProgram): void
    {
        $this->educationalProgramCacheService->clearCache($educationalProgram);
        $this->educationalProgramCacheService->clearAllCacheByModel();
    }
}