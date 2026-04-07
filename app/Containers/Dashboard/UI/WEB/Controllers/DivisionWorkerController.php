<?php

namespace App\Containers\Dashboard\UI\WEB\Controllers;

use App\Containers\Dashboard\Actions\Divisions\AttachDivisionWorkerAction;
use App\Containers\Dashboard\Actions\Divisions\DetachDivisionWorkerAction;
use App\Containers\Dashboard\Actions\Divisions\ListDivisionWorkersAction;
use App\Containers\Dashboard\Actions\Divisions\UpdateDivisionWorkerAction;
use App\Containers\Dashboard\UI\WEB\Requests\AttachDivisionWorkerRequest;
use App\Containers\InstituteStructure\Models\Division;
use App\Containers\User\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DivisionWorkerController extends Controller
{
    public function __construct(
        private readonly ListDivisionWorkersAction $listDivisionWorkersAction,
        private readonly AttachDivisionWorkerAction $attachDivisionWorkerAction,
        private readonly UpdateDivisionWorkerAction $updateDivisionWorkerAction,
        private readonly DetachDivisionWorkerAction $detachDivisionWorkerAction,
    ) {}

    /**
     * Показывает список сотрудников подразделения
     */
    public function index(Division $division, Request $request): \Inertia\Response
    {
        $filters = $request->only(['search', 'position']);
        $data = $this->listDivisionWorkersAction->run($division, $filters);

        // Получаем список доступных пользователей для прикрепления
        $availableUsers = User::whereHas('userDetail')
            ->whereDoesntHave('divisions', fn($q) => $q->where('divisions.id', $division->id))
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Dashboard/Divisions/Workers/Index', [
            'division' => $division,
            'workers' => $data['workers'],
            'availableUsers' => $availableUsers,
            'filters' => $data['filters'],
        ]);
    }

    /**
     * Прикрепляет сотрудника к подразделению
     */
    public function attach(Division $division, AttachDivisionWorkerRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();
            $user = User::findOrFail($validated['user_id']);

            $this->attachDivisionWorkerAction->run($division, $user, $validated);

            return redirect()->route('dashboard.divisions.edit', $division->id)
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
    public function update(Division $division, User $worker, AttachDivisionWorkerRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();

            $this->updateDivisionWorkerAction->run($division, $worker, $validated);

            return redirect()->route('dashboard.divisions.edit', $division->id)
                ->with('success', 'Данные сотрудника обновлены!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении сотрудника: ' . $e->getMessage());
        }
    }

    /**
     * Открепляет сотрудника от подразделения
     */
    public function detach(Division $division, User $worker): RedirectResponse
    {
        try {
            $this->detachDivisionWorkerAction->run($division, $worker);

            return redirect()->route('dashboard.divisions.edit', $division->id)
                ->with('success', 'Сотрудник удален из подразделения!');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Ошибка при удалении сотрудника: ' . $e->getMessage());
        }
    }
}
