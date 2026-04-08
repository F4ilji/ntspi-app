<?php

namespace App\Services\App\Cache;

use Illuminate\Support\Facades\Redis;

abstract class AbstractCacheService
{
    public function clearCacheByPrefix(string $prefix): void
    {
        // Получаем префикс Redis из конфигурации (уровень Redis connection)
        $redisPrefix = config('database.redis.options.prefix', '');
        
        // Удаляем wildcard (*) из префикса для поиска
        $searchPrefix = rtrim($prefix, '*');
        
        // Получаем ключи через Redis (Redis::keys возвращает ключи УЖЕ с префиксом)
        $keys = Redis::keys($searchPrefix . '*');

        if (!empty($keys)) {
            foreach ($keys as $key) {
                // Redis::keys возвращает ключи с префиксом (напр., 'ntspi:page_data_abc')
                // Но Redis::del() тоже добавит префикс автоматически!
                // Поэтому нужно убрать префикс перед удалением
                $cleanedKey = ltrim(str_replace($redisPrefix, '', $key), ':');
                Redis::del($cleanedKey);
            }
        }
    }
}