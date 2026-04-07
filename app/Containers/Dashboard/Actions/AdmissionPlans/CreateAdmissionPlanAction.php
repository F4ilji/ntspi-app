<?php

namespace App\Containers\Dashboard\Actions\AdmissionPlans;

use App\Containers\Education\Models\AdmissionPlan;

class CreateAdmissionPlanAction
{
    public function run(array $data): AdmissionPlan
    {
        return AdmissionPlan::create($data);
    }
}
