<?php

namespace App\Containers\Science\UI\WEB\Controllers;

use App\Containers\Science\Models\AcademicJournal;
use App\Containers\Science\Models\JournalIssue;
use App\Containers\Science\UI\WEB\Transformers\AcademicJournalResource;
use App\Ship\Contracts\SeoServiceInterface;
use App\Ship\Controllers\Controller;
use App\Ship\Enums\CacheKeys;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class ClientAcademicJournalController extends Controller
{
    public function __construct(readonly SeoServiceInterface $seoPageProvider){}

    public function index(): \Inertia\Response
    {
        $journals = Cache::remember(
            CacheKeys::ACADEMIC_JOURNALS_PREFIX->value . 'list',
            now()->addWeek(),
            function () {
                return AcademicJournalResource::collection(
                    AcademicJournal::query()->get()
                );
            }
        );

        $seo = $this->seoPageProvider->getSeoForCurrentPage();

        return Inertia::render('Client/AcademicJournals/Index', compact('journals', 'seo'));
    }

    public function show(string $slug): \Inertia\Response
    {
        $journalData = Cache::remember(
            CacheKeys::ACADEMIC_JOURNAL_PREFIX->value . $slug,
            now()->addWeek(),
            function () use ($slug) {
                return AcademicJournal::query()
                    ->where('slug', $slug)
                    ->firstOrFail();
            }
        );

        $seo = Cache::remember(
            CacheKeys::ACADEMIC_JOURNAL_PREFIX->value . 'seo_' . $slug,
            now()->addWeek(),
            function () use ($journalData) {
                return $this->seoPageProvider->getSeoForModel($journalData);
            }
        );

        $journal = new AcademicJournalResource($journalData);



        // Кешируем выпуски журнала, сгруппированные по годам
        $journals = Cache::remember(
            CacheKeys::ACADEMIC_JOURNAL_PREFIX->value . 'issues_' . $slug,
            now()->addWeek(),
            function () use ($journal) {
                $journalIssues = JournalIssue::where('academic_journal_id', $journal->id)
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


        return Inertia::render('Client/AcademicJournals/Show', compact('journal', 'journals', 'seo'));
    }
}
