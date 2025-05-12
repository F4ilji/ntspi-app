<?php

namespace App\Services\App\Cache;

use App\Containers\InstituteStructure\Models\Division;
use Illuminate\Support\Facades\Cache;

class DivisionCacheService extends AbstractCacheService implements CacheInterface
{
    private const CACHE_PREFIX = 'division_';
    private const ALL_CACHE_KEY = 'division_all';
    private const DEFAULT_TTL = 3600;

    public function clearCache($entity): void
    {
        if ($entity instanceof Division) {
            $this->clearAllCacheByModel();
        }
    }

    public function clearAllCacheByModel(): void
    {
        $this->clearCacheByPrefix(self::CACHE_PREFIX.'*');
        $this->clearAllDivisionsCache();
    }

    public function getCachedData(string $key)
    {
        return Cache::get($key);
    }

    public function cacheData(string $key, $data, int $ttl = null): void
    {
        Cache::put($key, $data, $ttl ?? self::DEFAULT_TTL);
    }

    public function getCacheKey(int $id): string
    {
        return self::CACHE_PREFIX . $id;
    }

    public function getAllCacheKey(): string
    {
        return self::ALL_CACHE_KEY;
    }

    private function forgetDivisionCache(int $id): void
    {
        Cache::forget($this->getCacheKey($id));
    }

    private function clearAllDivisionsCache(): void
    {
        Cache::forget(self::ALL_CACHE_KEY);
    }
}