<?php

namespace App\Services\App\Cache;

use App\Containers\Science\Models\AcademicJournal;
use App\Ship\Enums\CacheKeys;
use Illuminate\Support\Facades\Cache;

class AcademicJournalCacheService extends AbstractCacheService implements CacheInterface
{
    private const DEFAULT_TTL = 3600;

    public function clearCache($entity): void
    {
        if ($entity instanceof AcademicJournal) {
            $this->clearAllCacheByModel();
        }
    }

    public function clearAllCacheByModel(): void
    {
        $this->clearCacheByPrefix(CacheKeys::ACADEMIC_JOURNAL_PREFIX->value.'*');
        $this->clearCacheByPrefix(CacheKeys::ACADEMIC_JOURNALS_PREFIX->value.'*');
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