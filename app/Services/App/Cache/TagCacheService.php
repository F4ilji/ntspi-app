<?php

namespace App\Services\App\Cache;

use Illuminate\Support\Facades\Cache;

class TagCacheService extends AbstractCacheService implements CacheInterface
{
    /**
     * Очищает кеш, связанный с постом.
     *
     * @param mixed $entity Пост или связанная сущность
     * @return void
     */
    public function clearCache($entity): void
    {
    }

    public function clearAllCacheByModel(): void
    {
        $this->clearCacheByPrefix('tag_ids*');
        $this->clearCacheByPrefix('tags*');
        $this->clearCacheByPrefix('tag_content_*');
    }


    /**
     * Получает кешированные данные по ключу.
     *
     * @param string $key Ключ кеша
     * @return mixed
     */
    public function getCachedData(string $key)
    {
        return Cache::get($key);
    }

    /**
     * Кеширует данные по ключу.
     *
     * @param string $key Ключ кеша
     * @param mixed $data Данные для кеширования
     * @param int $ttl Время жизни кеша в секундах
     * @return void
     */
    public function cacheData(string $key, $data, int $ttl = 3600): void
    {
        Cache::put($key, $data, $ttl);
    }
}