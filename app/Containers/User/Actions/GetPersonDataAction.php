<?php

namespace App\Containers\User\Actions;

use App\Containers\User\Tasks\FindPersonBySlugTask;
use App\Containers\User\Tasks\GetSeoForPersonTask;

class GetPersonDataAction
{
    public function __construct(
        private readonly FindPersonBySlugTask $findPersonBySlugTask,
        private readonly GetSeoForPersonTask $getSeoForPersonTask,
    ) {}

    public function run(string $slug): array
    {
        $personData = $this->findPersonBySlugTask->run($slug);
        $seo = $this->getSeoForPersonTask->run($personData);

        return compact('personData', 'seo');
    }
}
