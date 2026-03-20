<?php

namespace App\Containers\Science\UI\WEB\Controllers;

use App\Containers\Science\Actions\GetAllAcademicJournalsAction;
use App\Containers\Science\Actions\GetAcademicJournalAction;
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
    public function __construct(
        readonly SeoServiceInterface $seoPageProvider,
        private readonly GetAllAcademicJournalsAction $getAllAcademicJournalsAction,
        private readonly GetAcademicJournalAction $getAcademicJournalAction
    ){}

    public function index(): \Inertia\Response
    {
        $result = $this->getAllAcademicJournalsAction->run();

        return Inertia::render('Client/AcademicJournals/Index', [
            'journals' => $result['journals'],
            'seo' => $result['seo'],
        ]);
    }

    public function show(string $slug): \Inertia\Response
    {
        $result = $this->getAcademicJournalAction->run($slug);

        $journal = new AcademicJournalResource($result['journalData']);

        return Inertia::render('Client/AcademicJournals/Show', [
            'journal' => $journal,
            'journals' => $result['groupedIssues'],
            'seo' => $result['seo'],
        ]);
    }
}
