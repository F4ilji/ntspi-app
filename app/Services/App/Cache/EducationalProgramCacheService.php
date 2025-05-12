<?php

namespace App\Services\App\Cache;

use App\Containers\Education\Models\EducationalProgram;
use App\Ship\Enums\CacheKeys;
use Illuminate\Support\Facades\Cache;

class EducationalProgramCacheService extends AbstractCacheService implements CacheInterface
{
    private const DEFAULT_TTL = 3600;

    public function clearCache($entity): void
    {
        if ($entity instanceof EducationalProgram) {
            $this->clearAllCacheByModel();

        }
    }

    public function clearAllCacheByModel(): void
    {
        $this->clearCacheByPrefix(CacheKeys::EDUCATION_PROGRAMS_PREFIX->value.'*');
        $this->clearCacheByPrefix(CacheKeys::EDUCATION_PROGRAM_PREFIX->value.'*');

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
        return CacheKeys::EDUCATION_PROGRAM_PREFIX->value . $id;
    }

    private function forgetProgramCache(int $id): void
    {
        Cache::forget($this->getCacheKey($id));
    }

    private function clearAllProgramsCache(): void
    {
        Cache::forget(CacheKeys::EDUCATION_PROGRAMS_PREFIX->value . '*');
    }
}