<?php

namespace App\Observers;

use App\Models\Category;
use App\Services\App\Cache\CategoryCacheService;

class CategoryObserver
{
    protected CategoryCacheService $categoryCacheService;

    public function __construct()
    {
        $this->categoryCacheService = new CategoryCacheService();
    }

    /**
     * Handle the Post "created" event.
     */
    public function saved(Category $category): void
    {
        $this->categoryCacheService->clearAllCacheByModel();
    }

    /**
     * Handle the Post "updated" event.
     */
    public function updated(Category $category)
    {
        $this->categoryCacheService->clearAllCacheByModel();
    }

    /**
     * Очистка кеша при удалении поста.
     */
    public function deleted(Category $category)
    {
        $this->categoryCacheService->clearAllCacheByModel();
    }

    /**
     * Handle the Post "restored" event.
     */
    public function restored(Category $category): void
    {
        //
    }

    /**
     * Handle the Post "force deleted" event.
     */
    public function forceDeleted(Category $category): void
    {
        //
    }
}
