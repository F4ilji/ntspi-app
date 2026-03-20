<?php

namespace App\Containers\Education\UI\API\Controllers;

use App\Containers\Education\Jobs\CreateAdmissionPlan;
use App\Http\Controllers\Controller;

class UpdateAdmissionPlansDataApiController extends Controller
{

    public function index()
    {
        $this->clearAndCreateAdmissionPlans();
        return redirect()->route('index');
    }

    private function clearAndCreateAdmissionPlans() : void
    {
        dispatch(new CreateAdmissionPlan());
    }
}
