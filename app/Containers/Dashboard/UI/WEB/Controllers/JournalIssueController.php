<?php

namespace App\Containers\Dashboard\UI\WEB\Controllers;

use App\Containers\Dashboard\Actions\JournalIssues\CreateJournalIssueAction;
use App\Containers\Dashboard\Actions\JournalIssues\DeleteJournalIssueAction;
use App\Containers\Dashboard\Actions\JournalIssues\ListJournalIssuesAction;
use App\Containers\Dashboard\Actions\JournalIssues\UpdateJournalIssueAction;
use App\Containers\Dashboard\UI\WEB\Requests\StoreJournalIssueRequest;
use App\Containers\Dashboard\UI\WEB\Requests\UpdateJournalIssueRequest;
use App\Containers\Science\Models\AcademicJournal;
use App\Containers\Science\Models\JournalIssue;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class JournalIssueController extends Controller
{
    public function __construct(
        private readonly ListJournalIssuesAction $listJournalIssuesAction,
        private readonly CreateJournalIssueAction $createJournalIssueAction,
        private readonly UpdateJournalIssueAction $updateJournalIssueAction,
        private readonly DeleteJournalIssueAction $deleteJournalIssueAction,
    ) {}

    /**
     * Display a listing of journal issues for a journal
     */
    public function index(AcademicJournal $academicJournal, Request $request): Response
    {
        $filters = $request->only(['search', 'year_publication', 'is_active']);

        $data = $this->listJournalIssuesAction->run($academicJournal->id, $filters);

        return Inertia::render('Dashboard/AcademicJournals/JournalIssues/Index', array_merge($data, [
            'journal' => $academicJournal->only(['id', 'title', 'slug']),
        ]));
    }

    /**
     * Show the form for creating a new journal issue
     */
    public function create(AcademicJournal $academicJournal): Response
    {
        return Inertia::render('Dashboard/AcademicJournals/JournalIssues/Create', [
            'journal' => $academicJournal->only(['id', 'title', 'slug']),
        ]);
    }

    /**
     * Store a newly created journal issue
     */
    public function store(StoreJournalIssueRequest $request, AcademicJournal $academicJournal): RedirectResponse
    {
        try {
            $validated = $request->validated();
            $validated['academic_journal_id'] = $academicJournal->id;

            $this->createJournalIssueAction->run($validated);

            return redirect()->route('dashboard.academic-journals.issues.index', $academicJournal->id)
                ->with('success', 'Выпуск журнала успешно создан!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Ошибка при создании выпуска: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified journal issue
     */
    public function edit(AcademicJournal $academicJournal, int $issue): Response
    {
        $issue = $academicJournal->journals()->findOrFail($issue);

        return Inertia::render('Dashboard/AcademicJournals/JournalIssues/Edit', [
            'journal' => $academicJournal->only(['id', 'title', 'slug']),
            'issue' => $issue,
        ]);
    }

    /**
     * Update the specified journal issue
     */
    public function update(UpdateJournalIssueRequest $request, AcademicJournal $academicJournal, int $issue): RedirectResponse
    {
        $issue = $academicJournal->journals()->findOrFail($issue);

        try {
            $validated = $request->validated();

            $this->updateJournalIssueAction->run($issue, $validated);

            return redirect()->route('dashboard.academic-journals.issues.index', $academicJournal->id)
                ->with('success', 'Выпуск журнала успешно обновлен!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении выпуска: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified journal issue
     */
    public function destroy(AcademicJournal $academicJournal, int $issue): RedirectResponse
    {
        $issue = $academicJournal->journals()->findOrFail($issue);

        try {
            $this->deleteJournalIssueAction->run($issue);

            return redirect()->route('dashboard.academic-journals.issues.index', $academicJournal->id)
                ->with('success', 'Выпуск журнала успешно удален!');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Ошибка при удалении выпуска: ' . $e->getMessage());
        }
    }
}
