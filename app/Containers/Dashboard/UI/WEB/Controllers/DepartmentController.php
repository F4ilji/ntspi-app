<?php

namespace App\Containers\Dashboard\UI\WEB\Controllers;

use App\Containers\Dashboard\Actions\Departments\CreateDepartmentAction;
use App\Containers\Dashboard\Actions\Departments\UpdateDepartmentAction;
use App\Containers\Dashboard\Actions\Departments\DeleteDepartmentAction;
use App\Containers\Dashboard\Actions\Departments\ListDepartmentsAction;
use App\Containers\Dashboard\Actions\Departments\ListDepartmentWorkersAction;
use App\Containers\Dashboard\Actions\Departments\ListDepartmentTeachersAction;
use App\Containers\Dashboard\Actions\Departments\ListDepartmentProgramsAction;
use App\Containers\Dashboard\UI\WEB\Requests\StoreDepartmentRequest;
use App\Containers\Dashboard\UI\WEB\Requests\UpdateDepartmentRequest;
use App\Containers\Education\Models\EducationalProgram;
use App\Containers\InstituteStructure\Models\Department;
use App\Containers\InstituteStructure\Models\Faculty;
use App\Containers\User\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DepartmentController extends Controller
{
    public function __construct(
        private readonly ListDepartmentsAction $listDepartmentsAction,
        private readonly ListDepartmentWorkersAction $listDepartmentWorkersAction,
        private readonly ListDepartmentTeachersAction $listDepartmentTeachersAction,
        private readonly ListDepartmentProgramsAction $listDepartmentProgramsAction,
        private readonly CreateDepartmentAction $createDepartmentAction,
        private readonly UpdateDepartmentAction $updateDepartmentAction,
        private readonly DeleteDepartmentAction $deleteDepartmentAction,
    ) {}

    /**
     * Показывает список кафедр
     */
    public function index(Request $request): \Inertia\Response
    {
        $filters = $request->only(['search', 'faculty_id', 'is_active']);

        $data = $this->listDepartmentsAction->run($filters);

        // Добавляем список факультетов для фильтра
        $data['faculties'] = Faculty::query()
            ->orderBy('title')
            ->get(['id', 'title']);

        return Inertia::render('Dashboard/Departments/Index', $data);
    }

    /**
     * Показывает форму создания кафедры
     */
    public function create(): \Inertia\Response
    {
        return Inertia::render('Dashboard/Departments/Create', [
            'faculties' => Faculty::query()
                ->orderBy('title')
                ->get(['id', 'title']),
        ]);
    }

    /**
     * Создает новую кафедру
     */
    public function store(StoreDepartmentRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();

            $this->createDepartmentAction->run($validated);

            return redirect()->route('dashboard.departments.index')
                ->with('success', 'Кафедра успешно создана!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Ошибка при создании кафедры: ' . $e->getMessage());
        }
    }

    /**
     * Показывает форму редактирования кафедры
     */
    public function edit(Department $department): \Inertia\Response
    {
        $department->load(['faculty', 'seo']);

        // Получаем список работников
        $workersData = $this->listDepartmentWorkersAction->run($department, []);
        $teachersData = $this->listDepartmentTeachersAction->run($department, []);
        $programsData = $this->listDepartmentProgramsAction->run($department, []);

        // Получаем список доступных пользователей
        $availableWorkers = User::whereHas('userDetail')
            ->whereDoesntHave('departments_work', fn($q) => $q->where('departments.id', $department->id))
            ->orderBy('name')
            ->get(['id', 'name']);

        $availableTeachers = User::whereHas('userDetail')
            ->whereDoesntHave('departments_teach', fn($q) => $q->where('departments.id', $department->id))
            ->orderBy('name')
            ->get(['id', 'name']);

        // Получаем список доступных программ (только опубликованные)
        $availablePrograms = EducationalProgram::where('status', 'published')
            ->whereDoesntHave('departments', fn($q) => $q->where('departments.id', $department->id))
            ->orderBy('name')
            ->get(['id', 'name', 'status']);

        return Inertia::render('Dashboard/Departments/Edit', [
            'department' => $department,
            'workers' => $workersData['workers'],
            'availableWorkers' => $availableWorkers,
            'teachers' => $teachersData['teachers'],
            'availableTeachers' => $availableTeachers,
            'programs' => $programsData['programs'],
            'availablePrograms' => $availablePrograms,
            'faculties' => Faculty::query()
                ->orderBy('title')
                ->get(['id', 'title']),
        ]);
    }

    /**
     * Обновляет существующую кафедру
     */
    public function update(UpdateDepartmentRequest $request, Department $department): RedirectResponse
    {
        try {
            $validated = $request->validated();

            $this->updateDepartmentAction->run($department, $validated);

            return redirect()->route('dashboard.departments.index')
                ->with('success', 'Кафедра успешно обновлена!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении кафедры: ' . $e->getMessage());
        }
    }

    /**
     * Удаляет кафедру
     */
    public function destroy(Department $department): RedirectResponse
    {
        try {
            $this->deleteDepartmentAction->run($department);

            return redirect()->route('dashboard.departments.index')
                ->with('success', 'Кафедра успешно удалена!');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Ошибка при удалении кафедры: ' . $e->getMessage());
        }
    }
}
