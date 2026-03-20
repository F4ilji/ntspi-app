<?php

namespace App\Services\App\Cache;

interface CacheInterface
{
    /**
     * Очищает кеш, связанный с конкретной сущностью.
     *
     * @param mixed $entity
     * @return void
     */
    public function clearCache($entity): void;

    /**
     * Получает кешированные данные.
     *
     * @param string $key
     * @return mixed
     */
    public function getCachedData(string $key);

    /**
     * Кеширует данные.
     *
     * @param string $key
     * @param mixed $data
     * @param int $ttl Время жизни кеша в секундах
     * @return void
     */
    public function cacheData(string $key, $data, int $ttl = 3600): void;

}