<?php

namespace App\Containers\User\Tasks;

use App\Containers\User\Models\User;
use App\Ship\Contracts\SeoServiceInterface;
use App\Ship\Enums\CacheKeys;
use App\Ship\Models\Seo;
use Illuminate\Support\Facades\Cache;

class GetSeoForPersonTask
{
    public function __construct(readonly SeoServiceInterface $seoPageProvider){}

    public function run(User $personData): array|null
    {
        return Cache::remember(
            CacheKeys::USER_PREFIX->value . 'seo_' . $personData->slug,
            now()->addHours(24),
            fn() => $this->seoPageProvider->getSeoForModel($personData)
        );
    }
}
