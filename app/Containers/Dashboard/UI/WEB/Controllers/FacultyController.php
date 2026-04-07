<?php

namespace App\Containers\Dashboard\UI\WEB\Controllers;

use App\Containers\Dashboard\Actions\Faculties\CreateFacultyAction;
use App\Containers\Dashboard\Actions\Faculties\UpdateFacultyAction;
use App\Containers\Dashboard\Actions\Faculties\DeleteFacultyAction;
use App\Containers\Dashboard\Actions\Faculties\ListFacultyWorkersAction;
use App\Containers\Dashboard\Actions\Faculties\ListFacultiesAction;
use App\Containers\Dashboard\UI\WEB\Requests\StoreFacultyRequest;
use App\Containers\Dashboard\UI\WEB\Requests\UpdateFacultyRequest;
use App\Containers\InstituteStructure\Models\Faculty;
use App\Containers\User\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FacultyController extends Controller
{
    public function __construct(
        private readonly ListFacultiesAction $listFacultiesAction,
        private readonly ListFacultyWorkersAction $listFacultyWorkersAction,
        private readonly CreateFacultyAction $createFacultyAction,
        private readonly UpdateFacultyAction $updateFacultyAction,
        private readonly DeleteFacultyAction $deleteFacultyAction,
    ) {}

    /**
     * Показывает список факультетов
     */
    public function index(Request $request): \Inertia\Response
    {
        $filters = $request->only(['search', 'is_active']);

        $data = $this->listFacultiesAction->run($filters);

        return Inertia::render('Dashboard/Faculties/Index', $data);
    }

    /**
     * Показывает форму создания факультета
     */
    public function create(): \Inertia\Response
    {
        return Inertia::render('Dashboard/Faculties/Create');
    }

    /**
     * Создает новый факультет
     */
    public function store(StoreFacultyRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();

            $this->createFacultyAction->run($validated);

            return redirect()->route('dashboard.faculties.index')
                ->with('success', 'Факультет успешно создан!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Ошибка при создании факультета: ' . $e->getMessage());
        }
    }

    /**
     * Показывает форму редактирования факультета
     */
    public function edit(Faculty $faculty): \Inertia\Response
    {
        $faculty->load(['seo']);

        // Получаем список работников
        $workersData = $this->listFacultyWorkersAction->run($faculty, []);
        
        // Получаем список доступных пользователей
        $availableWorkers = User::whereHas('userDetail')
            ->whereDoesntHave('faculties', fn($q) => $q->where('faculties.id', $faculty->id))
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Dashboard/Faculties/Edit', [
            'faculty' => $faculty,
            'workers' => $workersData['workers'],
            'availableWorkers' => $availableWorkers,
        ]);
    }

    /**
     * Обновляет существующий факультет
     */
    public function update(UpdateFacultyRequest $request, Faculty $faculty): RedirectResponse
    {
        try {
            $validated = $request->validated();

            $this->updateFacultyAction->run($faculty, $validated);

            return redirect()->route('dashboard.faculties.index')
                ->with('success', 'Факультет успешно обновлен!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении факультета: ' . $e->getMessage());
        }
    }

    /**
     * Удаляет факультет
     */
    public function destroy(Faculty $faculty): RedirectResponse
    {
        try {
            $this->deleteFacultyAction->run($faculty);

            return redirect()->route('dashboard.faculties.index')
                ->with('success', 'Факультет успешно удален!');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Ошибка при удалении факультета: ' . $e->getMessage());
        }
    }
}
