<?php

namespace App\Containers\Dashboard\UI\WEB\Controllers;

use App\Containers\Dashboard\Actions\Divisions\CreateDivisionAction;
use App\Containers\Dashboard\Actions\Divisions\UpdateDivisionAction;
use App\Containers\Dashboard\Actions\Divisions\DeleteDivisionAction;
use App\Containers\Dashboard\Actions\Divisions\ListDivisionWorkersAction;
use App\Containers\Dashboard\Actions\Divisions\ListDivisionsAction;
use App\Containers\Dashboard\UI\WEB\Requests\StoreDivisionRequest;
use App\Containers\Dashboard\UI\WEB\Requests\UpdateDivisionRequest;
use App\Containers\InstituteStructure\Models\Division;
use App\Containers\User\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DivisionController extends Controller
{
    public function __construct(
        private readonly ListDivisionsAction $listDivisionsAction,
        private readonly ListDivisionWorkersAction $listDivisionWorkersAction,
        private readonly CreateDivisionAction $createDivisionAction,
        private readonly UpdateDivisionAction $updateDivisionAction,
        private readonly DeleteDivisionAction $deleteDivisionAction,
    ) {}

    /**
     * Показывает список подразделений
     */
    public function index(Request $request): \Inertia\Response
    {
        $filters = $request->only(['search', 'is_active']);

        $data = $this->listDivisionsAction->run($filters);

        return Inertia::render('Dashboard/Divisions/Index', $data);
    }

    /**
     * Показывает форму создания подразделения
     */
    public function create(): \Inertia\Response
    {
        return Inertia::render('Dashboard/Divisions/Create');
    }

    /**
     * Создает новое подразделение
     */
    public function store(StoreDivisionRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();

            $this->createDivisionAction->run($validated);

            return redirect()->route('dashboard.divisions.index')
                ->with('success', 'Подразделение успешно создано!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Ошибка при создании подразделения: ' . $e->getMessage());
        }
    }

    /**
     * Показывает форму редактирования подразделения
     */
    public function edit(Division $division): \Inertia\Response
    {
        $division->load(['seo']);

        // Получаем список работников
        $workersData = $this->listDivisionWorkersAction->run($division, []);
        
        // Получаем список доступных пользователей
        $availableWorkers = User::whereHas('userDetail')
            ->whereDoesntHave('divisions', fn($q) => $q->where('divisions.id', $division->id))
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Dashboard/Divisions/Edit', [
            'division' => $division,
            'workers' => $workersData['workers'],
            'availableWorkers' => $availableWorkers,
        ]);
    }

    /**
     * Обновляет существующее подразделение
     */
    public function update(UpdateDivisionRequest $request, Division $division): RedirectResponse
    {
        try {
            $validated = $request->validated();

            $this->updateDivisionAction->run($division, $validated);

            return redirect()->route('dashboard.divisions.index')
                ->with('success', 'Подразделение успешно обновлено!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении подразделения: ' . $e->getMessage());
        }
    }

    /**
     * Удаляет подразделение
     */
    public function destroy(Division $division): RedirectResponse
    {
        try {
            $this->deleteDivisionAction->run($division);

            return redirect()->route('dashboard.divisions.index')
                ->with('success', 'Подразделение успешно удалено!');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Ошибка при удалении подразделения: ' . $e->getMessage());
        }
    }
}
