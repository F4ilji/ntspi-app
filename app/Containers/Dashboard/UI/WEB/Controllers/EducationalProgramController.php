<?php

namespace App\Containers\Dashboard\UI\WEB\Controllers;

use App\Containers\Education\Models\EducationalProgram;
use App\Containers\Dashboard\Actions\EducationalPrograms\CreateEducationalProgramAction;
use App\Containers\Dashboard\Actions\EducationalPrograms\DeleteEducationalProgramAction;
use App\Containers\Dashboard\Actions\EducationalPrograms\ListEducationalProgramsAction;
use App\Containers\Dashboard\Actions\EducationalPrograms\UpdateEducationalProgramAction;
use App\Containers\Dashboard\UI\WEB\Requests\StoreEducationalProgramRequest;
use App\Containers\Dashboard\UI\WEB\Requests\UpdateEducationalProgramRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EducationalProgramController extends Controller
{
    public function __construct(
        private readonly ListEducationalProgramsAction $listEducationalProgramsAction,
        private readonly CreateEducationalProgramAction $createEducationalProgramAction,
        private readonly UpdateEducationalProgramAction $updateEducationalProgramAction,
        private readonly DeleteEducationalProgramAction $deleteEducationalProgramAction,
    ) {}

    public function index(Request $request): \Inertia\Response
    {
        $filters = $request->only(['search', 'lvl_edu', 'status', 'direction_study_id']);
        $data = $this->listEducationalProgramsAction->run($filters);

        return Inertia::render('Dashboard/EducationalPrograms/Index', $data);
    }

    public function create(): \Inertia\Response
    {
        $data = $this->listEducationalProgramsAction->run([]);

        return Inertia::render('Dashboard/EducationalPrograms/Create', [
            'statuses' => $data['statuses'],
            'educationLevels' => $data['educationLevels'],
            'directionStudies' => $data['directionStudies'],
        ]);
    }

    public function store(StoreEducationalProgramRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();
            $this->createEducationalProgramAction->run($validated);

            return redirect()->route('dashboard.educational-programs.index')
                ->with('success', 'Образовательная программа успешно создана!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Ошибка при создании программы: ' . $e->getMessage());
        }
    }

    public function edit(EducationalProgram $educationalProgram): \Inertia\Response
    {
        $educationalProgram->load(['directionStudy']);
        $data = $this->listEducationalProgramsAction->run([]);

        return Inertia::render('Dashboard/EducationalPrograms/Edit', [
            'program' => $educationalProgram,
            'statuses' => $data['statuses'],
            'educationLevels' => $data['educationLevels'],
            'directionStudies' => $data['directionStudies'],
        ]);
    }

    public function update(UpdateEducationalProgramRequest $request, EducationalProgram $educationalProgram): RedirectResponse
    {
        try {
            $validated = $request->validated();
            $this->updateEducationalProgramAction->run($educationalProgram, $validated);

            return redirect()->route('dashboard.educational-programs.index')
                ->with('success', 'Образовательная программа успешно обновлена!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Ошибка при обновлении программы: ' . $e->getMessage());
        }
    }

    public function destroy(EducationalProgram $educationalProgram): RedirectResponse
    {
        try {
            $this->deleteEducationalProgramAction->run($educationalProgram);

            return redirect()->route('dashboard.educational-programs.index')
                ->with('success', 'Образовательная программа успешно удалена!');
        } catch (\Exception $e) {
            return back()->with('error', 'Ошибка при удалении программы: ' . $e->getMessage());
        }
    }
}
