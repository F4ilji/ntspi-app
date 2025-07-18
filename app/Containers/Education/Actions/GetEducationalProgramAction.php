<?php

namespace App\Containers\Education\Actions;

use App\Containers\Education\UI\WEB\Transformers\EducationalProgramResource;
use App\Ship\Contracts\SeoServiceInterface;
use App\Containers\Education\Tasks\GetEducationalProgramBySlugTask;
use App\Containers\Education\Tasks\GetEducationalFormsTask;
use App\Containers\Education\Tasks\GetSeoForModelTask;

class GetEducationalProgramAction
{
    public function __construct(
        private readonly GetEducationalProgramBySlugTask $getEducationalProgramBySlugTask,
        private readonly GetEducationalFormsTask $getEducationalFormsTask,
        private readonly GetSeoForModelTask $getSeoForModelTask,
        private readonly SeoServiceInterface $seoPageProvider,
    ) {}

    public function run(string $slug)
    {
        $programModel = $this->getEducationalProgramBySlugTask->run($slug);
        $formsEdu = $this->getEducationalFormsTask->run();
        $seo = $this->getSeoForModelTask->run($this->seoPageProvider, $programModel);

        $program = new EducationalProgramResource($programModel);

        return compact('program', 'formsEdu', 'seo');
    }
}
