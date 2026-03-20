<?php

namespace App\Containers\Widget\UI\API\Controllers;

use App\Containers\Widget\Actions\GetAdditionalEducationalProgramsAction;
use App\Ship\Controllers\Controller;

class ClientWidgetAdditionalEducationalProgramController extends Controller
{
    public function __construct(
        private readonly GetAdditionalEducationalProgramsAction $getAdditionalEducationalProgramsAction
    ) {}

    public function index()
    {
        return $this->getAdditionalEducationalProgramsAction->run();
    }
}
