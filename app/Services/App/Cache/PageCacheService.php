<?php

namespace App\Services\App\Cache;

use App\Models\Page;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class PageCacheService extends AbstractCacheService implements CacheInterface
{
    /**
     * Очищает кеш, связанный с постом.
     *
     * @param mixed $entity Пост или связанная сущность
     * @return void
     */
    public function clearCache($entity): void
    {
        $cacheKeyByPath = md5($entity->path);
        $cacheKeyById = md5($entity->id);



        Cache::forget('page_' . $cacheKeyByPath);
        Cache::forget('page_' . $cacheKeyById);

        Cache::forget('navigation');

    }

    public function clearAllCacheByModel(): void
    {
        $this->clearCacheByPrefix('page_*');
        $this->clearCacheByPrefix('page_data_*');
        Cache::forget('navigation');

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