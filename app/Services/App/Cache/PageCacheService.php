<?php

namespace App\Services\App\Cache;

use App\Enums\CacheKeys;
use App\Models\Page;
use Illuminate\Support\Facades\Cache;

class PageCacheService extends AbstractCacheService implements CacheInterface
{
    private const DEFAULT_TTL = 3600;

    public function clearCache($entity): void
    {
        if ($entity instanceof Page) {
            $this->clearAllCacheByModel();
            $this->clearNavigationCache();
        }
    }

    public function clearAllCacheByModel(): void
    {
        $this->clearCacheByPrefix(CacheKeys::PAGE_PREFIX->value.'*');
        $this->clearCacheByPrefix(CacheKeys::PAGE_DATA_PREFIX->value.'*');
        $this->clearNavigationCache();
    }

    public function getCachedData(string $key)
    {
        return Cache::get($key);
    }

    public function cacheData(string $key, $data, int $ttl = null): void
    {
        Cache::put($key, $data, $ttl ?? self::DEFAULT_TTL);
    }


    private function clearNavigationCache(): void
    {
        Cache::forget(CacheKeys::NAVIGATION_PREFIX->value);
    }
}