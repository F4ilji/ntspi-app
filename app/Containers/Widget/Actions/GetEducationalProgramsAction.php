<?php

namespace App\Containers\Widget\Actions;

use App\Containers\Widget\Tasks\GetPublishedEducationalProgramsTask;

class GetEducationalProgramsAction
{
    public function __construct(
        private readonly GetPublishedEducationalProgramsTask $getPublishedEducationalProgramsTask
    ) {}

    public function run()
    {
        return $this->getPublishedEducationalProgramsTask->run();
    }
}
