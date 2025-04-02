<?php

namespace App\Services\App\Cache;

use App\Enums\CacheKeys;
use Illuminate\Support\Facades\Cache;

class TagCacheService extends AbstractCacheService implements CacheInterface
{
    private const DEFAULT_TTL = 3600;

    public function clearCache($entity): void
    {
        $this->clearAllCacheByModel();
    }

    public function clearAllCacheByModel(): void
    {
        $this->clearTagIdsCache();
        $this->clearTagsCache();
        $this->clearTagContentCache();
    }

    public function getCachedData(string $key)
    {
        return Cache::get($key);
    }

    public function cacheData(string $key, $data, int $ttl = null): void
    {
        Cache::put($key, $data, $ttl ?? self::DEFAULT_TTL);
    }

    private function clearTagIdsCache(): void
    {
        $this->clearCacheByPrefix(CacheKeys::TAG_IDS_PREFIX->value.'*');
    }

    private function clearTagsCache(): void
    {
        $this->clearCacheByPrefix(CacheKeys::TAGS_PREFIX->value.'*');
    }

    private function clearTagContentCache(): void
    {
        $this->clearCacheByPrefix(CacheKeys::TAG_CONTENT_PREFIX->value.'*');
    }
}