<?php

namespace App\Services\App\Cache;

use App\Ship\Enums\CacheKeys;
use Illuminate\Support\Facades\Cache;

class CategoryCacheService extends AbstractCacheService implements CacheInterface
{
    private const DEFAULT_TTL = 3600;

    public function clearCache($entity): void
    {
        $this->clearAllCacheByModel();
    }

    public function clearAllCacheByModel(): void
    {
        $this->clearCacheByPrefix(CacheKeys::CATEGORIES_PREFIX->value.'*');
    }

    public function getCachedData(string $key)
    {
        return Cache::get($key);
    }

    public function cacheData(string $key, $data, int $ttl = null): void
    {
        Cache::put($key, $data, $ttl ?? self::DEFAULT_TTL);
    }
}