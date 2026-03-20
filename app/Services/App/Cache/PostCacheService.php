<?php

namespace App\Services\App\Cache;

use App\Ship\Enums\CacheKeys;
use Illuminate\Support\Facades\Cache;

class PostCacheService extends AbstractCacheService implements CacheInterface
{
    private const DEFAULT_TTL = 3600;

    /**
     * Clear cache for specific post
     */
    public function clearCache($entity): void
    {
        $this->forgetPostCache($entity->slug, $entity->id);
        $this->clearRecentPostsCache();
    }

    /**
     * Clear all cache related to posts
     */
    public function clearAllCacheByModel(): void
    {
        $this->clearCacheByPrefix(CacheKeys::POST_PREFIX->value.'*');
        $this->clearCacheByPrefix(CacheKeys::POSTS_PREFIX->value.'*');
        $this->clearRecentPostsCache();
    }

    public function getCachedData(string $key)
    {
        return Cache::get($key);
    }

    public function cacheData(string $key, $data, int $ttl = null): void
    {
        Cache::put($key, $data, $ttl ?? self::DEFAULT_TTL);
    }

    /**
     * Forget cache for specific post by slug and id
     */
    private function forgetPostCache(string $slug, int $id): void
    {
        Cache::forget(CacheKeys::POST_PREFIX->value.md5($slug));
        Cache::forget(CacheKeys::POST_PREFIX->value.md5($id));
    }

    /**
     * Clear recent posts cache
     */
    private function clearRecentPostsCache(): void
    {
        $this->clearCacheByPrefix(CacheKeys::RECENT_POSTS_PREFIX->value.'*');
    }
}