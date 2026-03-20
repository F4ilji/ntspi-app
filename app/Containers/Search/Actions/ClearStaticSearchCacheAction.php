<?php

namespace App\Containers\Search\Actions;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ClearStaticSearchCacheAction
{
    const CACHE_KEY = 'static_html_index_v2';

    public function run(): bool
    {
        try {
            Cache::forget(self::CACHE_KEY);
            return true;
        } catch (\Exception $e) {
            Log::error('Cache clear error: ' . $e->getMessage());
            return false;
        }
    }
}
