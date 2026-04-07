<?php

namespace App\Containers\Dashboard\Actions\AdmissionPlans;

use App\Containers\Education\Models\AdmissionPlan;

class UpdateAdmissionPlanAction
{
    public function run(AdmissionPlan $plan, array $data): AdmissionPlan
    {
        $plan->update($data);
        return $plan;
    }
}
