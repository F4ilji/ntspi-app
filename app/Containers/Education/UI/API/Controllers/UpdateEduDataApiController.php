<?php

namespace App\Containers\Education\UI\API\Controllers;

use App\Http\Controllers\Controller;
use App\Jobs\CreateDirectionStudy;
use App\Jobs\CreateEducationalProgram;
use App\Services\Vicon\EducationalProgram\EducationalProgramService;

class UpdateEduDataApiController extends Controller
{

    public function index()
    {
        $this->updateOrCreateDirectionStudy();
        $this->updateOrCreateEducationProgram();
        return redirect()->route('index');
    }

    private function updateOrCreateDirectionStudy() : void
    {
        dispatch(new CreateDirectionStudy());
    }
    private function updateOrCreateEducationProgram() : void
    {
        dispatch(new CreateEducationalProgram());
    }
}
