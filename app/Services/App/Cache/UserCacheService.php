<?php

namespace App\Services\App\Cache;

use App\Ship\Enums\CacheKeys;
use App\Containers\User\Models\User;
use Illuminate\Support\Facades\Cache;

class UserCacheService extends AbstractCacheService implements CacheInterface
{
    private const DEFAULT_TTL = 3600;

    public function clearCache($entity): void
    {
        if ($entity instanceof User) {
            $this->clearAllCacheByModel();
        }
    }

    public function clearAllCacheByModel(): void
    {
        $this->clearCacheByPrefix(CacheKeys::USER_PREFIX->value.'*');
        $this->clearCacheByPrefix(CacheKeys::FACULTY_PREFIX->value.'*');
        $this->clearCacheByPrefix(CacheKeys::DEPARTMENT_PREFIX->value.'*');
        $this->clearCacheByPrefix(CacheKeys::DIVISION_PREFIX->value.'*');
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