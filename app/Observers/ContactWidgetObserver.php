<?php

namespace App\Observers;

use App\Models\ContactWidget;
use App\Services\App\Cache\ContactWidgetCacheService;

class ContactWidgetObserver
{
    private ContactWidgetCacheService $contactWidgetCacheService;

    public function __construct()
    {
        $this->contactWidgetCacheService = app(ContactWidgetCacheService::class);
    }

    /**
     * Handle the ContactWidget "created" event.
     */
    public function created(ContactWidget $contactWidget): void
    {
        $this->contactWidgetCacheService->clearAllCacheByModel();
    }

    /**
     * Handle the ContactWidget "updated" event.
     */
    public function updated(ContactWidget $contactWidget): void
    {
        $this->contactWidgetCacheService->clearCache($contactWidget);
        $this->contactWidgetCacheService->clearAllCacheByModel();
    }

    /**
     * Handle the ContactWidget "deleted" event.
     */
    public function deleted(ContactWidget $contactWidget): void
    {
        $this->contactWidgetCacheService->clearCache($contactWidget);
        $this->contactWidgetCacheService->clearAllCacheByModel();
    }

    /**
     * Handle the ContactWidget "restored" event.
     */
    public function restored(ContactWidget $contactWidget): void
    {
        $this->contactWidgetCacheService->clearAllCacheByModel();
    }

    /**
     * Handle the ContactWidget "force deleted" event.
     */
    public function forceDeleted(ContactWidget $contactWidget): void
    {
        $this->contactWidgetCacheService->clearCache($contactWidget);
        $this->contactWidgetCacheService->clearAllCacheByModel();
    }
}