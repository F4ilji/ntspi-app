<?php

namespace App\Containers\Dashboard\Actions\AdmissionPlans;

use App\Containers\Education\Models\AdmissionPlan;

class DeleteAdmissionPlanAction
{
    public function run(AdmissionPlan $plan): bool
    {
        return $plan->delete();
    }
}
