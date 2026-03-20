<?php

namespace App\Services\App\Cache;

use Illuminate\Support\Facades\Redis;

abstract class AbstractCacheService
{
    public function clearCacheByPrefix(string $prefix): void
    {
        // Получаем префикс из .env
        $redisPrefix = config('database.redis.options.prefix', 'ntspi');

        // Получаем ключи, соответствующие префиксу
        $keys = Redis::keys($prefix);

        if (!empty($keys)) {
            foreach ($keys as $key) {
                // Убираем префикс и двоеточие из ключа
                $cleanedKey = str_replace([$redisPrefix, ':'], '', $key);

                // Удаляем ключ
                Redis::del($cleanedKey);
            }
        }
    }
}