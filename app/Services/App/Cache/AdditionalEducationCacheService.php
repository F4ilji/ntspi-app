<?php

namespace App\Services\App\Cache;

use App\Enums\CacheKeys;
use App\Models\AdditionalEducation;
use Illuminate\Support\Facades\Cache;

class AdditionalEducationCacheService extends AbstractCacheService implements CacheInterface
{
    private const DEFAULT_TTL = 3600;

    public function clearCache($entity): void
    {
        if ($entity instanceof AdditionalEducation) {
            $this->clearAllCacheByModel();
        }
    }

    public function clearAllCacheByModel(): void
    {
        $this->clearCacheByPrefix(CacheKeys::ADDITIONAL_EDUCATIONAL_PROGRAM_PREFIX->value.'*');
        $this->clearCacheByPrefix(CacheKeys::ADDITIONAL_EDUCATIONAL_PROGRAMS_PREFIX->value.'*');
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