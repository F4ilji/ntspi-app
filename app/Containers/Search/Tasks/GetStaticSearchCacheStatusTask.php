<?php

namespace App\Containers\Search\Tasks;

use Illuminate\Support\Facades\Cache;

class GetStaticSearchCacheStatusTask
{
    const CACHE_KEY = 'static_html_index_v2';
    const FILES_DIR = 'sveden';

    public function run(): array
    {
        return [
            'exists' => Cache::has(self::CACHE_KEY),
            'ttl' => Cache::get(self::CACHE_KEY.'_ttl', null),
            'driver' => config('cache.default'),
            'path' => public_path(self::FILES_DIR),
            'directory_exists' => is_dir(public_path(self::FILES_DIR))
        ];
    }
}
