<?php

namespace App\Http\Controllers;

use App\Enums\CacheKeys;
use App\Http\Resources\ClientAcademicJournalListResource;
use App\Models\AcademicJournal;
use App\Models\JournalIssue;
use App\Services\App\Breadcrumb\BreadcrumbService;
use App\Services\App\Seo\SeoPageProvider;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class ClientAcademicJournalController extends Controller
{
    public function __construct(readonly SeoPageProvider $seoPageProvider){}

    public function index()
    {
        $journals = Cache::remember(
            CacheKeys::ACADEMIC_JOURNALS_PREFIX->value . 'list',
            now()->addWeek(), // Кешируем на неделю, так как журналы меняются редко
            function () {
                return ClientAcademicJournalListResource::collection(
                    AcademicJournal::query()->get()
                );
            }
        );

        $seo = $this->seoPageProvider->getSeoForCurrentPage();

        return Inertia::render('Client/AcademicJournals/Index', compact('journals', 'seo'));
    }

    public function show(string $slug)
    {
        // Кешируем основной журнал
        [$journal, $seo] = Cache::remember(
            CacheKeys::ACADEMIC_JOURNAL_PREFIX->value . $slug,
            now()->addWeek(),
            function () use ($slug) {
                $journal = AcademicJournal::query()
                    ->where('slug', $slug)
                    ->firstOrFail();
                $seo = $this->seoPageProvider->getSeoForModel($journal);
                return [
                    new ClientAcademicJournalListResource($journal),
                    $seo
                ];
            }
        );

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
