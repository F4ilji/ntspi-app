<?php

namespace App\Containers\Education\UI\API\Controllers;

use App\Containers\Education\Jobs\CreateDirectionStudy;
use App\Containers\Education\Jobs\CreateEducationalProgram;
use App\Http\Controllers\Controller;

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
