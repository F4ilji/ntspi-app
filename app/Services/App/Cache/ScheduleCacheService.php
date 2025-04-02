<?php

namespace App\Services\App\Cache;

use App\Enums\CacheKeys;
use App\Models\Schedule;
use Illuminate\Support\Facades\Cache;

class ScheduleCacheService extends AbstractCacheService implements CacheInterface
{
    private const DEFAULT_TTL = 3600;

    public function clearCache($entity): void
    {
        if ($entity instanceof Schedule) {
            $this->clearAllCacheByModel();
        }
    }

    public function clearAllCacheByModel(): void
    {
        $this->clearCacheByPrefix(CacheKeys::SCHEDULE_PREFIX->value.'*');
        $this->clearAllSchedulesCache();
    }

    public function getCachedData(string $key)
    {
        return Cache::get($key);
    }

    public function cacheData(string $key, $data, int $ttl = null): void
    {
        Cache::put($key, $data, $ttl ?? self::DEFAULT_TTL);
    }


    private function forgetScheduleCache(int $id): void
    {
        Cache::forget($this->getCacheKey($id));
    }

    private function clearAllSchedulesCache(): void
    {
        Cache::forget(CacheKeys::SCHEDULES_PREFIX->value);
    }
}