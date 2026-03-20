<?php

namespace App\Containers\Science\Actions;

use App\Containers\Science\Tasks\GetAllAcademicJournalsTask;
use App\Containers\Science\UI\WEB\Transformers\AcademicJournalResource;
use App\Ship\Contracts\SeoServiceInterface;

class GetAllAcademicJournalsAction
{
    public function __construct(
        private readonly GetAllAcademicJournalsTask $getAllAcademicJournalsTask,
        private readonly SeoServiceInterface $seoService
    ) {
    }

    public function run(): array
    {
        $journals = $this->getAllAcademicJournalsTask->run();
        $seo = $this->seoService->getSeoForCurrentPage();

        return [
            'journals' => AcademicJournalResource::collection($journals),
            'seo' => $seo,
        ];
    }
}
