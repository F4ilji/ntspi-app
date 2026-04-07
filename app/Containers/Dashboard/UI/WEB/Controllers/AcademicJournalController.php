<?php

namespace App\Containers\Dashboard\UI\WEB\Controllers;

use App\Containers\Dashboard\Actions\AcademicJournals\CreateAcademicJournalAction;
use App\Containers\Dashboard\Actions\AcademicJournals\DeleteAcademicJournalAction;
use App\Containers\Dashboard\Actions\AcademicJournals\ListAcademicJournalsAction;
use App\Containers\Dashboard\Actions\AcademicJournals\UpdateAcademicJournalAction;
use App\Containers\Dashboard\UI\WEB\Requests\StoreAcademicJournalRequest;
use App\Containers\Dashboard\UI\WEB\Requests\UpdateAcademicJournalRequest;
use App\Containers\Science\Models\AcademicJournal;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AcademicJournalController extends Controller
{
    public function __construct(
        private readonly ListAcademicJournalsAction $listAcademicJournalsAction,
        private readonly CreateAcademicJournalAction $createAcademicJournalAction,
        private readonly UpdateAcademicJournalAction $updateAcademicJournalAction,
        private readonly DeleteAcademicJournalAction $deleteAcademicJournalAction,
    ) {}

    /**
     * Display a listing of academic journals
     */
    public function index(Request $request): Response
    {
        $filters = $request->only(['search']);

        $data = $this->listAcademicJournalsAction->run($filters);

        return Inertia::render('Dashboard/AcademicJournals/Index', $data);
    }

    /**
     * Show the form for creating a new journal
     */
    public function create(): Response
    {
        return Inertia::render('Dashboard/AcademicJournals/Create');
    }

    /**
     * Store a newly created journal
     */
    public function store(StoreAcademicJournalRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();

            $this->createAcademicJournalAction->run($validated);

            return redirect()->route('dashboard.academic-journals.index')
                ->with('success', 'Научный журнал успешно создан!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Ошибка при создании журнала: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified journal
     */
    public function edit(AcademicJournal $academicJournal): Response
    {
        return Inertia::render('Dashboard/AcademicJournals/Edit', [
            'journal' => $academicJournal,
        ]);
    }

    /**
     * Update the specified journal
     */
    public function update(UpdateAcademicJournalRequest $request, AcademicJournal $academicJournal): RedirectResponse
    {
        try {
            $validated = $request->validated();

            $this->updateAcademicJournalAction->run($academicJournal, $validated);

            return redirect()->route('dashboard.academic-journals.index')
                ->with('success', 'Научный журнал успешно обновлен!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении журнала: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified journal
     */
    public function destroy(AcademicJournal $academicJournal): RedirectResponse
    {
        try {
            $this->deleteAcademicJournalAction->run($academicJournal);

            return redirect()->route('dashboard.academic-journals.index')
                ->with('success', 'Научный журнал успешно удален!');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Ошибка при удалении журнала: ' . $e->getMessage());
        }
    }
}
