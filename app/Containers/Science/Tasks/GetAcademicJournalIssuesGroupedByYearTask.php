<?php

namespace App\Containers\Science\Tasks;

use App\Containers\Science\Models\JournalIssue;
use App\Containers\Science\UI\WEB\Transformers\AcademicJournalResource;
use App\Ship\Enums\CacheKeys;
use Illuminate\Support\Facades\Cache;

class GetAcademicJournalIssuesGroupedByYearTask
{
    public function run(int $academicJournalId): array
    {
        return Cache::remember(
            CacheKeys::ACADEMIC_JOURNAL_PREFIX->value . 'issues_' . $academicJournalId,
            now()->addWeek(),
            function () use ($academicJournalId) {
                $journalIssues = JournalIssue::where('academic_journal_id', $academicJournalId)
                    ->get()
                    ->groupBy('year_publication');

                $groupedIssues = [];
                foreach ($journalIssues as $year => $journalGroup) {
                    $groupedIssues[] = [
                        'year_publication' => $year,
                        'journalIssues' => $journalGroup
                    ];
                }

                return $groupedIssues;
            }
        );
    }
}
