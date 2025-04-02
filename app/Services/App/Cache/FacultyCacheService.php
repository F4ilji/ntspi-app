<?php

namespace App\Services\App\Cache;

use App\Enums\CacheKeys;
use App\Models\Faculty;
use Illuminate\Support\Facades\Cache;

class FacultyCacheService extends AbstractCacheService implements CacheInterface
{
    private const DEFAULT_TTL = 3600;

    public function clearCache($entity): void
    {
        if ($entity instanceof Faculty) {
            $this->clearAllCacheByModel();

        }
    }

    public function clearAllCacheByModel(): void
    {
        $this->clearCacheByPrefix(CacheKeys::FACULTY_PREFIX->value.'*');
        $this->clearAllFacultiesCache();
    }

    public function getCachedData(string $key)
    {
        return Cache::get($key);
    }

    public function cacheData(string $key, $data, int $ttl = null): void
    {
        Cache::put($key, $data, $ttl ?? self::DEFAULT_TTL);
    }


    private function clearAllFacultiesCache(): void
    {
        Cache::forget(CacheKeys::FACULTIES_PREFIX->value.'*');
    }
}