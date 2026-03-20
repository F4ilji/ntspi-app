<?php

namespace App\Containers\Science\Actions;

use App\Containers\Science\Tasks\FindAcademicJournalBySlugTask;
use App\Containers\Science\Tasks\GetAcademicJournalIssuesGroupedByYearTask;
use App\Ship\Contracts\SeoServiceInterface;
use App\Ship\Exceptions\NotFoundException;
use Intervention\Image\Exception\NotFoundException as ExceptionNotFoundException;

class GetAcademicJournalAction
{
    public function __construct(
        private readonly FindAcademicJournalBySlugTask         $findAcademicJournalBySlugTask,
        private readonly GetAcademicJournalIssuesGroupedByYearTask $getAcademicJournalIssuesGroupedByYearTask,
        private readonly SeoServiceInterface                   $seoService
    )
    {
    }

    /**
     * @throws NotFoundException
     */
    public function run(string $slug): array
    {
        $journalData = $this->findAcademicJournalBySlugTask->run($slug);

        $seo = $this->seoService->getSeoForModel($journalData);
        $groupedIssues = $this->getAcademicJournalIssuesGroupedByYearTask->run($journalData->id);

        return [
            'journalData' => $journalData,
            'seo' => $seo,
            'groupedIssues' => $groupedIssues,
        ];
    }
}
