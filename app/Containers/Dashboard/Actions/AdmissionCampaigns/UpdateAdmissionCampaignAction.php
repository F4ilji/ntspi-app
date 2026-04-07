<?php

namespace App\Containers\Dashboard\Actions\AdmissionCampaigns;

use App\Containers\Education\Models\AdmissionCampaign;

class UpdateAdmissionCampaignAction
{
    public function run(AdmissionCampaign $campaign, array $data): AdmissionCampaign
    {
        $campaign->update($data);
        return $campaign;
    }
}
