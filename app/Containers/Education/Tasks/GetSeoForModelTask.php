<?php

namespace App\Containers\Education\Tasks;

use App\Ship\Contracts\SeoServiceInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class GetSeoForModelTask
{
    public function run(SeoServiceInterface $seoPageProvider, Model $model)
    {
        $cacheKeySeo = 'education_program_seo_' . md5($model->slug);
        return Cache::remember($cacheKeySeo, now()->addHours(1), function () use ($seoPageProvider, $model) {
            return $seoPageProvider->getSeoForModel($model);
        });
    }
}
