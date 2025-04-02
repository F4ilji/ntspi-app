<?php

namespace App\Services\App\Cache;

use App\Enums\CacheKeys;
use App\Models\PageReferenceList;
use Illuminate\Support\Facades\Cache;

class PageReferenceListCacheService extends AbstractCacheService implements CacheInterface
{
    private const DEFAULT_TTL = 3600;

    public function clearCache($entity): void
    {
        if ($entity instanceof PageReferenceList) {
            $this->clearAllCacheByModel();

        }
    }

    public function clearAllCacheByModel(): void
    {
        $this->clearCacheByPrefix(CacheKeys::PAGE_REFERENCE_LIST_PREFIX->value.'*');
        $this->clearAllReferencesCache();
    }

    public function getCachedData(string $key)
    {
        return Cache::get($key);
    }

    public function cacheData(string $key, $data, int $ttl = null): void
    {
        Cache::put($key, $data, $ttl ?? self::DEFAULT_TTL);
    }


    private function clearAllReferencesCache(): void
    {
        Cache::forget(CacheKeys::PAGE_REFERENCE_LISTS_PREFIX->value.'*');
    }
}