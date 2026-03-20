<?php

namespace App\Containers\Widget\UI\API\Controllers;

use App\Containers\Widget\Actions\GetEducationalProgramsAction;
use App\Ship\Controllers\Controller;

class ClientWidgetEducationalProgramController extends Controller
{
    public function __construct(
        private readonly GetEducationalProgramsAction $getEducationalProgramsAction
    ) {}

    public function index()
    {
        return $this->getEducationalProgramsAction->run();
    }
}
