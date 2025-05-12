<?php

namespace App\Services\App\Cache;

use App\Ship\Enums\CacheKeys;
use App\Containers\Event\Models\Event;
use Illuminate\Support\Facades\Cache;

class EventCacheService extends AbstractCacheService implements CacheInterface
{
    private const DEFAULT_TTL = 3600;

    public function clearCache($entity): void
    {
        if ($entity instanceof Event) {
            $this->clearAllCacheByModel();

        }
    }

    public function clearAllCacheByModel(): void
    {
        $this->clearCacheByPrefix(CacheKeys::EVENT_PREFIX->value.'*');
        $this->clearAllEventsCache();
    }

    public function getCachedData(string $key)
    {
        return Cache::get($key);
    }

    public function cacheData(string $key, $data, int $ttl = null): void
    {
        Cache::put($key, $data, $ttl ?? self::DEFAULT_TTL);
    }


    private function clearAllEventsCache(): void
    {
        Cache::forget(CacheKeys::EVENTS_PREFIX->value.'*');
    }
}