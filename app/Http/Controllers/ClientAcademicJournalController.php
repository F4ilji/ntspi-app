<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClientAcademicJournalListResource;
use App\Http\Resources\ClientVirtualExhibitionListResource;
use App\Models\AcademicJournal;
use App\Models\JournalIssue;
use App\Models\VirtualExhibition;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClientAcademicJournalController extends Controller
{
    public function index()
    {
        $journals = ClientAcademicJournalListResource::collection(AcademicJournal::query()->get());

        return Inertia::render('Client/AcademicJournals/Index', compact('journals'));
    }

    public function show(string $slug)
    {
        $journal = new ClientAcademicJournalListResource(AcademicJournal::query()->where('slug', '=', $slug)->firstOrFail());
        $journalIssues = JournalIssue::where('academic_journal_id', $journal->id)
            ->groupBy('year_publication')->get();

        $journals = [];

        foreach ($journalIssues as $year => $journalGroup) {
            $journals[] = [
                'year_publication' => $year,
                'journalIssues' => $journalGroup
            ];
        }
        return Inertia::render('Client/AcademicJournals/Show', compact('journal', 'journals'));
    }
}
