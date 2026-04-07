<?php

namespace App\Containers\Dashboard\Actions\AdmissionCampaigns;

use App\Containers\Education\Models\AdmissionCampaign;

class CreateAdmissionCampaignAction
{
    public function run(array $data): AdmissionCampaign
    {
        return AdmissionCampaign::create($data);
    }
}
