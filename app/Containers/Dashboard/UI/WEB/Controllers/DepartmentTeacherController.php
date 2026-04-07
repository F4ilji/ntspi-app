<?php

namespace App\Containers\Dashboard\UI\WEB\Controllers;

use App\Containers\Dashboard\Actions\Departments\AttachDepartmentTeacherAction;
use App\Containers\Dashboard\Actions\Departments\DetachDepartmentTeacherAction;
use App\Containers\Dashboard\Actions\Departments\ListDepartmentTeachersAction;
use App\Containers\Dashboard\Actions\Departments\UpdateDepartmentTeacherAction;
use App\Containers\Dashboard\UI\WEB\Requests\AttachDepartmentTeacherRequest;
use App\Containers\InstituteStructure\Models\Department;
use App\Containers\User\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DepartmentTeacherController extends Controller
{
    public function __construct(
        private readonly ListDepartmentTeachersAction $listDepartmentTeachersAction,
        private readonly AttachDepartmentTeacherAction $attachDepartmentTeacherAction,
        private readonly UpdateDepartmentTeacherAction $updateDepartmentTeacherAction,
        private readonly DetachDepartmentTeacherAction $detachDepartmentTeacherAction,
    ) {}

    /**
     * Показывает список преподавателей кафедры
     */
    public function index(Department $department, Request $request): \Inertia\Response
    {
        $filters = $request->only(['search', 'position']);
        $data = $this->listDepartmentTeachersAction->run($department, $filters);

        // Получаем список доступных пользователей для прикрепления
        $availableUsers = User::whereHas('userDetail')
            ->whereDoesntHave('departments_teach', fn($q) => $q->where('departments.id', $department->id))
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Dashboard/Departments/Teachers/Index', [
            'department' => $department,
            'teachers' => $data['teachers'],
            'availableUsers' => $availableUsers,
            'filters' => $data['filters'],
        ]);
    }

    /**
     * Прикрепляет преподавателя к кафедре
     */
    public function attach(Department $department, AttachDepartmentTeacherRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();
            $user = User::findOrFail($validated['user_id']);

            $this->attachDepartmentTeacherAction->run($department, $user, $validated);

            return redirect()->route('dashboard.departments.edit', $department->id)
                ->with('success', 'Преподаватель успешно добавлен!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Ошибка при добавлении преподавателя: ' . $e->getMessage());
        }
    }

    /**
     * Обновляет данные преподавателя
     */
    public function update(Department $department, User $teacher, AttachDepartmentTeacherRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();

            $this->updateDepartmentTeacherAction->run($department, $teacher, $validated);

            return redirect()->route('dashboard.departments.edit', $department->id)
                ->with('success', 'Данные преподавателя обновлены!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении преподавателя: ' . $e->getMessage());
        }
    }

    /**
     * Открепляет преподавателя от кафедры
     */
    public function detach(Department $department, User $teacher): RedirectResponse
    {
        try {
            $this->detachDepartmentTeacherAction->run($department, $teacher);

            return redirect()->route('dashboard.departments.edit', $department->id)
                ->with('success', 'Преподаватель удален из кафедры!');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Ошибка при удалении преподавателя: ' . $e->getMessage());
        }
    }
}
