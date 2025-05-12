<?php

namespace App\Observers;

use App\Containers\Article\Models\Tag;
use App\Services\App\Cache\TagCacheService;

class TagObserver
{
    protected TagCacheService $tagCacheService;

    public function __construct()
    {
        $this->tagCacheService = new TagCacheService();
    }

    /**
     * Handle the Post "created" event.
     */
    public function saved(Tag $tag): void
    {
        $this->tagCacheService->clearAllCacheByModel();
    }

    /**
     * Handle the Post "updated" event.
     */
    public function updated(Tag $tag)
    {
        $this->tagCacheService->clearAllCacheByModel();
    }

    /**
     * Очистка кеша при удалении поста.
     */
    public function deleted(Tag $tag)
    {
        $this->tagCacheService->clearAllCacheByModel();
    }

    /**
     * Handle the Post "restored" event.
     */
    public function restored(Tag $tag): void
    {
        //
    }

    /**
     * Handle the Post "force deleted" event.
     */
    public function forceDeleted(Tag $tag): void
    {
        //
    }
}
