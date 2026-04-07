<?php

namespace App\Containers\Dashboard\UI\WEB\Controllers;

use App\Containers\Dashboard\Actions\Faculties\AttachFacultyWorkerAction;
use App\Containers\Dashboard\Actions\Faculties\DetachFacultyWorkerAction;
use App\Containers\Dashboard\Actions\Faculties\ListFacultyWorkersAction;
use App\Containers\Dashboard\Actions\Faculties\UpdateFacultyWorkerAction;
use App\Containers\Dashboard\UI\WEB\Requests\AttachFacultyWorkerRequest;
use App\Containers\InstituteStructure\Models\Faculty;
use App\Containers\User\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FacultyWorkerController extends Controller
{
    public function __construct(
        private readonly ListFacultyWorkersAction $listFacultyWorkersAction,
        private readonly AttachFacultyWorkerAction $attachFacultyWorkerAction,
        private readonly UpdateFacultyWorkerAction $updateFacultyWorkerAction,
        private readonly DetachFacultyWorkerAction $detachFacultyWorkerAction,
    ) {}

    /**
     * Показывает список сотрудников факультета
     */
    public function index(Faculty $faculty, Request $request): \Inertia\Response
    {
        $filters = $request->only(['search', 'position']);
        $data = $this->listFacultyWorkersAction->run($faculty, $filters);

        // Получаем список доступных пользователей для прикрепления
        $availableUsers = User::whereHas('userDetail')
            ->whereDoesntHave('faculties', fn($q) => $q->where('faculties.id', $faculty->id))
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Dashboard/Faculties/Workers/Index', [
            'faculty' => $faculty,
            'workers' => $data['workers'],
            'availableUsers' => $availableUsers,
            'filters' => $data['filters'],
        ]);
    }

    /**
     * Прикрепляет сотрудника к факультету
     */
    public function attach(Faculty $faculty, AttachFacultyWorkerRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();
            $user = User::findOrFail($validated['user_id']);

            $this->attachFacultyWorkerAction->run($faculty, $user, $validated);

            return redirect()->route('dashboard.faculties.edit', $faculty->id)
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
    public function update(Faculty $faculty, User $worker, AttachFacultyWorkerRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();

            $this->updateFacultyWorkerAction->run($faculty, $worker, $validated);

            return redirect()->route('dashboard.faculties.edit', $faculty->id)
                ->with('success', 'Данные сотрудника обновлены!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении сотрудника: ' . $e->getMessage());
        }
    }

    /**
     * Открепляет сотрудника от факультета
     */
    public function detach(Faculty $faculty, User $worker): RedirectResponse
    {
        try {
            $this->detachFacultyWorkerAction->run($faculty, $worker);

            return redirect()->route('dashboard.faculties.edit', $faculty->id)
                ->with('success', 'Сотрудник удален из факультета!');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Ошибка при удалении сотрудника: ' . $e->getMessage());
        }
    }
}
