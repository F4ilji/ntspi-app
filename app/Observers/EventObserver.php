<?php

namespace App\Observers;

use App\Models\Event;
use App\Services\App\Cache\EventCacheService;

class EventObserver
{
    private EventCacheService $eventCacheService;

    public function __construct()
    {
        $this->eventCacheService = app(EventCacheService::class);
    }

    /**
     * Handle the Event "created" event.
     */
    public function created(Event $event): void
    {
        $this->eventCacheService->clearAllCacheByModel();
    }

    /**
     * Handle the Event "updated" event.
     */
    public function updated(Event $event): void
    {
        $this->eventCacheService->clearCache($event);
        $this->eventCacheService->clearAllCacheByModel();
    }

    /**
     * Handle the Event "deleted" event.
     */
    public function deleted(Event $event): void
    {
        $this->eventCacheService->clearCache($event);
        $this->eventCacheService->clearAllCacheByModel();
    }

    /**
     * Handle the Event "restored" event.
     */
    public function restored(Event $event): void
    {
        $this->eventCacheService->clearAllCacheByModel();
    }

    /**
     * Handle the Event "force deleted" event.
     */
    public function forceDeleted(Event $event): void
    {
        $this->eventCacheService->clearCache($event);
        $this->eventCacheService->clearAllCacheByModel();
    }
}