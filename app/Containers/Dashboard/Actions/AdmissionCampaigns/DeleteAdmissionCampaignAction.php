<?php

namespace App\Containers\Dashboard\Actions\AdmissionCampaigns;

use App\Containers\Education\Models\AdmissionCampaign;

class DeleteAdmissionCampaignAction
{
    public function run(AdmissionCampaign $campaign): bool
    {
        return $campaign->delete();
    }
}
