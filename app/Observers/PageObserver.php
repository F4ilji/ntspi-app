<?php

namespace App\Observers;

use App\Models\Page;
use Illuminate\Support\Facades\Cache;

class PageObserver
{
    /**
     * Handle the Page "created" event.
     */
    public function created(Page $page): void
    {
        //
    }

    /**
     * Handle the Page "updated" event.
     */
    public function updated(Page $page)
    {
        $cacheKey = 'page_' . md5($page->path);
        Cache::forget($cacheKey);
    }

    /**
     * Handle the Page "deleted" event.
     */
    public function deleted(Page $page)
    {
        $cacheKey = 'page_' . md5($page->path);
        Cache::forget($cacheKey);
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
