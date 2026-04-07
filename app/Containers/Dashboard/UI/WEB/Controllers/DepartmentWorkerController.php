<?php

namespace App\Containers\Dashboard\UI\WEB\Controllers;

use App\Containers\Dashboard\Actions\Departments\AttachDepartmentWorkerAction;
use App\Containers\Dashboard\Actions\Departments\DetachDepartmentWorkerAction;
use App\Containers\Dashboard\Actions\Departments\ListDepartmentWorkersAction;
use App\Containers\Dashboard\Actions\Departments\UpdateDepartmentWorkerAction;
use App\Containers\Dashboard\UI\WEB\Requests\AttachDepartmentWorkerRequest;
use App\Containers\InstituteStructure\Models\Department;
use App\Containers\User\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DepartmentWorkerController extends Controller
{
    public function __construct(
        private readonly ListDepartmentWorkersAction $listDepartmentWorkersAction,
        private readonly AttachDepartmentWorkerAction $attachDepartmentWorkerAction,
        private readonly UpdateDepartmentWorkerAction $updateDepartmentWorkerAction,
        private readonly DetachDepartmentWorkerAction $detachDepartmentWorkerAction,
    ) {}

    /**
     * Показывает список сотрудников кафедры
     */
    public function index(Department $department, Request $request): \Inertia\Response
    {
        $filters = $request->only(['search', 'position']);
        $data = $this->listDepartmentWorkersAction->run($department, $filters);

        // Получаем список доступных пользователей для прикрепления
        $availableUsers = User::whereHas('userDetail')
            ->whereDoesntHave('departments_work', fn($q) => $q->where('departments.id', $department->id))
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Dashboard/Departments/Workers/Index', [
            'department' => $department,
            'workers' => $data['workers'],
            'availableUsers' => $availableUsers,
            'filters' => $data['filters'],
        ]);
    }

    /**
     * Прикрепляет сотрудника к кафедре
     */
    public function attach(Department $department, AttachDepartmentWorkerRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();
            $user = User::findOrFail($validated['user_id']);

            $this->attachDepartmentWorkerAction->run($department, $user, $validated);

            return redirect()->route('dashboard.departments.edit', $department->id)
                ->with('success', 'Сотрудник успешно добавлен!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Ошибка при добавлении сотрудника: ' . $e->getMessage());
        }
    }

    /**
     * Обновляет данные сотрудника
     */
    public function update(Department $department, User $worker, AttachDepartmentWorkerRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();

            $this->updateDepartmentWorkerAction->run($department, $worker, $validated);

            return redirect()->route('dashboard.departments.edit', $department->id)
                ->with('success', 'Данные сотрудника обновлены!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении сотрудника: ' . $e->getMessage());
        }
    }

    /**
     * Открепляет сотрудника от кафедры
     */
    public function detach(Department $department, User $worker): RedirectResponse
    {
        try {
            $this->detachDepartmentWorkerAction->run($department, $worker);

            return redirect()->route('dashboard.departments.edit', $department->id)
                ->with('success', 'Сотрудник удален из кафедры!');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Ошибка при удалении сотрудника: ' . $e->getMessage());
        }
    }
}
