<?php

namespace App\Containers\Dashboard\UI\WEB\Controllers;

use App\Containers\Dashboard\Actions\Departments\AttachDepartmentProgramAction;
use App\Containers\Dashboard\Actions\Departments\DetachDepartmentProgramAction;
use App\Containers\Dashboard\Actions\Departments\ListDepartmentProgramsAction;
use App\Containers\Dashboard\UI\WEB\Requests\AttachDepartmentProgramRequest;
use App\Containers\Education\Models\EducationalProgram;
use App\Containers\InstituteStructure\Models\Department;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DepartmentProgramController extends Controller
{
    public function __construct(
        private readonly ListDepartmentProgramsAction $listDepartmentProgramsAction,
        private readonly AttachDepartmentProgramAction $attachDepartmentProgramAction,
        private readonly DetachDepartmentProgramAction $detachDepartmentProgramAction,
    ) {}

    /**
     * Показывает список образовательных программ кафедры
     */
    public function index(Department $department, Request $request): \Inertia\Response
    {
        $filters = $request->only(['search', 'status']);
        $data = $this->listDepartmentProgramsAction->run($department, $filters);

        // Получаем список доступных программ для прикрепления (только опубликованные)
        $availablePrograms = EducationalProgram::where('status', 'published')
            ->whereDoesntHave('departments', fn($q) => $q->where('departments.id', $department->id))
            ->orderBy('name')
            ->get(['id', 'name', 'status']);

        return Inertia::render('Dashboard/Departments/Programs/Index', [
            'department' => $department,
            'programs' => $data['programs'],
            'availablePrograms' => $availablePrograms,
            'filters' => $data['filters'],
        ]);
    }

    /**
     * Прикрепляет программу к кафедре
     */
    public function attach(Department $department, AttachDepartmentProgramRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();
            $program = EducationalProgram::findOrFail($validated['program_id']);

            $this->attachDepartmentProgramAction->run($department, $program);

            return redirect()->route('dashboard.departments.edit', $department->id)
                ->with('success', 'Программа успешно добавлена!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Ошибка при добавлении программы: ' . $e->getMessage());
        }
    }

    /**
     * Открепляет программу от кафедры
     */
    public function detach(Department $department, EducationalProgram $program): RedirectResponse
    {
        try {
            $this->detachDepartmentProgramAction->run($department, $program);

            return redirect()->route('dashboard.departments.edit', $department->id)
                ->with('success', 'Программа удалена из кафедры!');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Ошибка при удалении программы: ' . $e->getMessage());
        }
    }
}
