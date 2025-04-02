<?php

namespace App\Observers;

use App\Models\Schedule;
use App\Services\App\Cache\ScheduleCacheService;

class ScheduleObserver
{
    private ScheduleCacheService $scheduleCacheService;

    public function __construct()
    {
        $this->scheduleCacheService = app(ScheduleCacheService::class);
    }

    /**
     * Handle the Schedule "created" event.
     */
    public function created(Schedule $schedule): void
    {
        $this->scheduleCacheService->clearAllCacheByModel();
    }

    /**
     * Handle the Schedule "updated" event.
     */
    public function updated(Schedule $schedule): void
    {
        $this->scheduleCacheService->clearCache($schedule);
        $this->scheduleCacheService->clearAllCacheByModel();
    }

    /**
     * Handle the Schedule "deleted" event.
     */
    public function deleted(Schedule $schedule): void
    {
        $this->scheduleCacheService->clearCache($schedule);
        $this->scheduleCacheService->clearAllCacheByModel();
    }

    /**
     * Handle the Schedule "restored" event.
     */
    public function restored(Schedule $schedule): void
    {
        $this->scheduleCacheService->clearAllCacheByModel();
    }

    /**
     * Handle the Schedule "force deleted" event.
     */
    public function forceDeleted(Schedule $schedule): void
    {
        $this->scheduleCacheService->clearCache($schedule);
        $this->scheduleCacheService->clearAllCacheByModel();
    }
}