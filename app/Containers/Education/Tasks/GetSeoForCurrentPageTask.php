<?php

namespace App\Containers\Education\Tasks;

use App\Ship\Contracts\SeoServiceInterface;
use Illuminate\Support\Facades\Cache;

class GetSeoForCurrentPageTask
{
    public function run(SeoServiceInterface $seoPageProvider)
    {
        $cacheKeySeo = 'education_programs_seo';
        return Cache::remember($cacheKeySeo, now()->addDay(), function () use ($seoPageProvider) {
            return $seoPageProvider->getSeoForCurrentPage();
        });
    }
}
