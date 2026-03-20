<?php

namespace App\Containers\Widget\Actions;

use App\Containers\Widget\Tasks\FindSliderBySlugTask;

class GetSliderBySlugAction
{
    public function __construct(
        private readonly FindSliderBySlugTask $findSliderBySlugTask
    ) {}

    public function run(string $slug): ?object
    {
        return $this->findSliderBySlugTask->run($slug);
    }
}
