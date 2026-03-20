<?php

namespace App\Containers\Education\Tasks;

use App\Containers\Education\Models\AdmissionCampaign;
use App\Ship\Enums\CacheKeys;
use Illuminate\Support\Facades\Cache;

class GetAdmissionCampaignNameTask
{
    public function run(): string
    {
        $cacheKey = CacheKeys::EDUCATION_PROGRAM_PREFIX->value . 'active_campaign_name';

        return Cache::remember($cacheKey, now()->addHours(1), function () {
            $campaign = AdmissionCampaign::query()->where('status', 1)->first();
            return $campaign->name;
        });
    }
}
