<?php

namespace App\Observers;

use App\Containers\Article\Models\Post;
use App\Services\VK\VkService;
use App\Services\App\Cache\PostCacheService;

class PostObserver
{
    protected PostCacheService $postCacheService;

    public function __construct()
    {
        $this->postCacheService = new PostCacheService();
    }

    /**
     * Handle the Post "created" event.
     */
    public function saved(Post $post): void
    {
        $this->postCacheService->clearAllCacheByModel();
    }

    /**
     * Handle the Post "updated" event.
     */
    public function updated(Post $post)
    {
        $this->postCacheService->clearAllCacheByModel();
    }

    /**
     * Очистка кеша при удалении поста.
     */
    public function deleted(Post $post)
    {
        $this->postCacheService->clearAllCacheByModel();
        app(VkService::class)->deletePost($post->id);
    }

    /**
     * Handle the Post "restored" event.
     */
    public function restored(Post $post): void
    {
        //
    }

    /**
     * Handle the Post "force deleted" event.
     */
    public function forceDeleted(Post $post): void
    {
        //
    }

}
