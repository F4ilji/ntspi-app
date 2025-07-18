<?php

namespace App\Containers\Education\Tasks;

use App\Containers\Education\Models\AdmissionCampaign;
use App\Ship\Enums\CacheKeys;
use Illuminate\Support\Facades\Cache;

class GetActiveAdmissionCampaignTask
{
    public function run()
    {
        return Cache::remember(CacheKeys::ADMISSION_CAMPAIGNS_PREFIX->value, now()->addDay(), function () {
            return AdmissionCampaign::where('status', 1)->first();
        });
    }
}
