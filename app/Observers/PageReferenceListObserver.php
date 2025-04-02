<?php

namespace App\Observers;

use App\Models\PageReferenceList;
use App\Services\App\Cache\PageReferenceListCacheService;

class PageReferenceListObserver
{
    private PageReferenceListCacheService $pageReferenceListCacheService;

    public function __construct()
    {
        $this->pageReferenceListCacheService = app(PageReferenceListCacheService::class);
    }

    /**
     * Handle the PageReferenceList "created" event.
     */
    public function created(PageReferenceList $pageReferenceList): void
    {
        $this->pageReferenceListCacheService->clearAllCacheByModel();
    }

    /**
     * Handle the PageReferenceList "updated" event.
     */
    public function updated(PageReferenceList $pageReferenceList): void
    {
        $this->pageReferenceListCacheService->clearCache($pageReferenceList);
        $this->pageReferenceListCacheService->clearAllCacheByModel();
    }

    /**
     * Handle the PageReferenceList "deleted" event.
     */
    public function deleted(PageReferenceList $pageReferenceList): void
    {
        $this->pageReferenceListCacheService->clearCache($pageReferenceList);
        $this->pageReferenceListCacheService->clearAllCacheByModel();
    }

    /**
     * Handle the PageReferenceList "restored" event.
     */
    public function restored(PageReferenceList $pageReferenceList): void
    {
        $this->pageReferenceListCacheService->clearAllCacheByModel();
    }

    /**
     * Handle the PageReferenceList "force deleted" event.
     */
    public function forceDeleted(PageReferenceList $pageReferenceList): void
    {
        $this->pageReferenceListCacheService->clearCache($pageReferenceList);
        $this->pageReferenceListCacheService->clearAllCacheByModel();
    }
}