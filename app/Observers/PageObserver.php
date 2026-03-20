<?php

namespace App\Observers;

use App\Containers\AppStructure\Models\Page;
use App\Services\App\Cache\PageCacheService;
use Illuminate\Support\Facades\Cache;

class PageObserver
{
    private PageCacheService $pageCacheService;

    public function __construct()
    {
        $this->pageCacheService = app(PageCacheService::class);
    }

    /**
     * Handle the Page "created" event.
     */
    public function created(Page $page): void
    {
        $this->pageCacheService->clearAllCacheByModel();
    }

    /**
     * Handle the Page "updated" event.
     */
    public function updated(Page $page): void
    {
        $this->pageCacheService->clearAllCacheByModel();
    }

    /**
     * Handle the Page "deleted" event.
     */
    public function deleted(Page $page): void
    {
        $this->pageCacheService->clearAllCacheByModel();
    }

    /**
     * Handle the Page "restored" event.
     */
    public function restored(Page $page): void
    {
        //
    }

    /**
     * Handle the Page "force deleted" event.
     */
    public function forceDeleted(Page $page): void
    {
        //
    }
}
