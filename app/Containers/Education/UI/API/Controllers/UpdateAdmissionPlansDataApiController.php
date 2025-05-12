<?php

namespace App\Containers\Education\UI\API\Controllers;

use App\Http\Controllers\Controller;
use App\Jobs\CreateAdmissionPlan;
use App\Jobs\CreateDirectionStudy;
use App\Jobs\CreateEducationalProgram;
use App\Services\Vicon\EducationalProgram\EducationalProgramService;

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
