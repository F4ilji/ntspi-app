<?php

namespace App\Observers;

use App\Models\Division;
use App\Services\App\Cache\DivisionCacheService;

class DivisionObserver
{
    private DivisionCacheService $divisionCacheService;

    public function __construct()
    {
        $this->divisionCacheService = app(DivisionCacheService::class);
    }

    /**
     * Handle the Division "created" event.
     */
    public function created(Division $division): void
    {
        $this->divisionCacheService->clearAllCacheByModel();
    }

    /**
     * Handle the Division "updated" event.
     */
    public function updated(Division $division): void
    {
        $this->divisionCacheService->clearCache($division);
        $this->divisionCacheService->clearAllCacheByModel();
    }

    /**
     * Handle the Division "deleted" event.
     */
    public function deleted(Division $division): void
    {
        $this->divisionCacheService->clearCache($division);
        $this->divisionCacheService->clearAllCacheByModel();
    }

    /**
     * Handle the Division "restored" event.
     */
    public function restored(Division $division): void
    {
        $this->divisionCacheService->clearAllCacheByModel();
    }

    /**
     * Handle the Division "force deleted" event.
     */
    public function forceDeleted(Division $division): void
    {
        $this->divisionCacheService->clearCache($division);
        $this->divisionCacheService->clearAllCacheByModel();
    }
}