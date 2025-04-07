<?php

namespace App\Observers;

use App\Services\App\Cache\AdmissionCampaignCacheService;

class AdmissionCampaignObserver
{
    public function __construct(
        private readonly AdmissionCampaignCacheService $admissionCampaignCacheService
    ){}

    public function saved(): void
    {
        $this->admissionCampaignCacheService->clearAllCacheByModel();
    }

    /**
     * Handle the Post "updated" event.
     */
    public function updated()
    {
        $this->admissionCampaignCacheService->clearAllCacheByModel();
    }

    /**
     * Очистка кеша при удалении поста.
     */
    public function deleted()
    {
        $this->admissionCampaignCacheService->clearAllCacheByModel();
    }

    /**
     * Handle the Post "restored" event.
     */
    public function restored(): void
    {
        //
    }

    /**
     * Handle the Post "force deleted" event.
     */
    public function forceDeleted(): void
    {
        //
    }
}
