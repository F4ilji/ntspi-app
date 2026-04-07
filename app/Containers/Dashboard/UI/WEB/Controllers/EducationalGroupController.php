<?php

namespace App\Containers\Dashboard\UI\WEB\Controllers;

use App\Containers\Dashboard\Actions\EducationalGroups\CreateEducationalGroupAction;
use App\Containers\Dashboard\Actions\EducationalGroups\UpdateEducationalGroupAction;
use App\Containers\Dashboard\Actions\EducationalGroups\DeleteEducationalGroupAction;
use App\Containers\Dashboard\Actions\EducationalGroups\ListEducationalGroupsAction;
use App\Containers\Dashboard\UI\WEB\Requests\StoreEducationalGroupRequest;
use App\Containers\Dashboard\UI\WEB\Requests\UpdateEducationalGroupRequest;
use App\Containers\Schedule\Models\EducationalGroup;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EducationalGroupController extends Controller
{
    public function __construct(
        private readonly ListEducationalGroupsAction $listEducationalGroupsAction,
        private readonly CreateEducationalGroupAction $createEducationalGroupAction,
        private readonly UpdateEducationalGroupAction $updateEducationalGroupAction,
        private readonly DeleteEducationalGroupAction $deleteEducationalGroupAction,
    ) {}

    /**
     * Показывает список учебных групп
     */
    public function index(Request $request): \Inertia\Response
    {
        $filters = $request->only(['search', 'faculty_id', 'education_form_id']);

        $data = $this->listEducationalGroupsAction->run($filters);

        return Inertia::render('Dashboard/EducationalGroups/Index', $data);
    }

    /**
     * Показывает форму создания группы
     */
    public function create(): \Inertia\Response
    {
        $data = $this->listEducationalGroupsAction->run([]);

        return Inertia::render('Dashboard/EducationalGroups/Create', [
            'faculties' => $data['faculties'],
            'educationForms' => $data['educationForms'],
        ]);
    }

    /**
     * Создает новую учебную группу
     */
    public function store(StoreEducationalGroupRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();

            $this->createEducationalGroupAction->run($validated);

            return redirect()->route('dashboard.educational-groups.index')
                ->with('success', 'Учебная группа успешно создана!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Ошибка при создании учебной группы: ' . $e->getMessage());
        }
    }

    /**
     * Показывает форму редактирования группы
     */
    public function edit(EducationalGroup $educationalGroup): \Inertia\Response
    {
        $educationalGroup->load(['faculty']);

        $data = $this->listEducationalGroupsAction->run([]);

        return Inertia::render('Dashboard/EducationalGroups/Edit', [
            'group' => $educationalGroup,
            'faculties' => $data['faculties'],
            'educationForms' => $data['educationForms'],
        ]);
    }

    /**
     * Обновляет существующую учебную группу
     */
    public function update(UpdateEducationalGroupRequest $request, EducationalGroup $educationalGroup): RedirectResponse
    {
        try {
            $validated = $request->validated();

            $this->updateEducationalGroupAction->run($educationalGroup, $validated);

            return redirect()->route('dashboard.educational-groups.index')
                ->with('success', 'Учебная группа успешно обновлена!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении учебной группы: ' . $e->getMessage());
        }
    }

    /**
     * Удаляет учебную группу
     */
    public function destroy(EducationalGroup $educationalGroup): RedirectResponse
    {
        try {
            $this->deleteEducationalGroupAction->run($educationalGroup);

            return redirect()->route('dashboard.educational-groups.index')
                ->with('success', 'Учебная группа успешно удалена!');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Ошибка при удалении учебной группы: ' . $e->getMessage());
        }
    }
}
