<?php

namespace App\Services\App\Cache;

use App\Ship\Enums\CacheKeys;
use Illuminate\Support\Facades\Cache;

class AdmissionCampaignCacheService extends AbstractCacheService implements CacheInterface
{
    private const DEFAULT_TTL = 3600;

    /**
     * Clear cache for specific post
     */
    public function clearCache($entity): void
    {

    }

    /**
     * Clear all cache related to posts
     */
    public function clearAllCacheByModel(): void
    {
        $this->clearCacheByPrefix(CacheKeys::ADMISSION_CAMPAIGNS_PREFIX->value.'*');
        $this->clearCacheByPrefix(CacheKeys::DIRECTION_STUDIES->value.'*');
        $this->clearCacheByPrefix(CacheKeys::ADMISSION_PLANS->value.'*');
        $this->clearCacheByPrefix(CacheKeys::EDUCATION_PROGRAM_PREFIX->value.'*');
        $this->clearCacheByPrefix('education_'.'*');
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