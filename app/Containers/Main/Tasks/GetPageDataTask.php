<?php

namespace App\Containers\Main\Tasks;

use App\Containers\AppStructure\Models\Page;
use Illuminate\Support\Facades\Cache;

class GetPageDataTask
{
    public function run()
    {
        $path = route('index', null, false);
        
        return Cache::remember('page_' . $path, now()->addHour(), function () use ($path) {
            return Page::where('path', $path)->first();
        });
    }
}