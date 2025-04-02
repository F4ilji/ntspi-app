<?php

namespace App\Observers;

use App\Models\AdditionalEducation;
use App\Services\App\Cache\AdditionalEducationCacheService;

class AdditionalEducationObserver
{
    private AdditionalEducationCacheService $additionalEducationCacheService;

    public function __construct()
    {
        $this->additionalEducationCacheService = app(AdditionalEducationCacheService::class);
    }

    /**
     * Handle the AdditionalEducation "created" event.
     */
    public function created(AdditionalEducation $additionalEducation): void
    {
        $this->additionalEducationCacheService->clearAllCacheByModel();
    }

    /**
     * Handle the AdditionalEducation "updated" event.
     */
    public function updated(AdditionalEducation $additionalEducation): void
    {
        $this->additionalEducationCacheService->clearCache($additionalEducation);
    }

    /**
     * Handle the AdditionalEducation "deleted" event.
     */
    public function deleted(AdditionalEducation $additionalEducation): void
    {
        $this->additionalEducationCacheService->clearAllCacheByModel();
    }

    /**
     * Handle the AdditionalEducation "restored" event.
     */
    public function restored(AdditionalEducation $additionalEducation): void
    {
        $this->additionalEducationCacheService->clearAllCacheByModel();
    }

    /**
     * Handle the AdditionalEducation "force deleted" event.
     */
    public function forceDeleted(AdditionalEducation $additionalEducation): void
    {
        $this->additionalEducationCacheService->clearAllCacheByModel();
    }
}