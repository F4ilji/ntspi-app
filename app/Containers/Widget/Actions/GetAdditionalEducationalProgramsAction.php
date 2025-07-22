<?php

namespace App\Containers\Widget\Actions;

use App\Containers\Widget\Tasks\GetActiveAdditionalEducationalProgramsTask;

class GetAdditionalEducationalProgramsAction
{
    public function __construct(
        private readonly GetActiveAdditionalEducationalProgramsTask $getActiveAdditionalEducationalProgramsTask
    ) {}

    public function run()
    {
        return $this->getActiveAdditionalEducationalProgramsTask->run();
    }
}
