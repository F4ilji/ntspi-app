<?php

namespace App\Observers;

use App\Models\Post;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redirect;

class PostObserver
{
    /**
     * Handle the Post "created" event.
     */
    public function saved(Post $post): void
    {

    }

    /**
     * Handle the Post "updated" event.
     */
    public function updated(Post $post)
    {
        $this->clearPostCache($post);
        $this->cachePost($post); // Кешируем обновленный пост
    }

    /**
     * Очистка кеша при удалении поста.
     */
    public function deleted(Post $post)
    {
        $this->clearPostCache($post);
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

    protected function cachePost(Post $post)
    {
        $cacheKey = 'post_' . md5($post->slug);
        $cacheData = [
            'post' => $post,
            'seo' => $post->seo,
        ];

        Cache::put($cacheKey, $cacheData, now()->addHours(1)); // Кешируем на 1 час
    }

    /**
     * Очистка кеша поста.
     */
    protected function clearPostCache(Post $post)
    {
        $cacheKey = 'post_' . md5($post->slug);
        Cache::forget($cacheKey);
    }
}
