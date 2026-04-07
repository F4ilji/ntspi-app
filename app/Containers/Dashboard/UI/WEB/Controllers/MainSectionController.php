<?php

namespace App\Containers\Dashboard\UI\WEB\Controllers;

use App\Containers\AppStructure\Models\MainSection;
use App\Containers\Dashboard\Actions\MainSections\CreateMainSectionAction;
use App\Containers\Dashboard\Actions\MainSections\DeleteMainSectionAction;
use App\Containers\Dashboard\Actions\MainSections\ListMainSectionsAction;
use App\Containers\Dashboard\Actions\MainSections\UpdateMainSectionAction;
use App\Containers\Dashboard\UI\WEB\Requests\StoreMainSectionRequest;
use App\Containers\Dashboard\UI\WEB\Requests\UpdateMainSectionRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MainSectionController extends Controller
{
    public function __construct(
        private readonly ListMainSectionsAction $listMainSectionsAction,
        private readonly CreateMainSectionAction $createMainSectionAction,
        private readonly UpdateMainSectionAction $updateMainSectionAction,
        private readonly DeleteMainSectionAction $deleteMainSectionAction,
    ) {}

    /**
     * Показывает список главных разделов
     */
    public function index(Request $request): \Inertia\Response
    {
        $filters = $request->only(['search']);

        $data = $this->listMainSectionsAction->run($filters);

        return Inertia::render('Dashboard/MainSections/Index', $data);
    }

    /**
     * Показывает форму создания раздела
     */
    public function create(): \Inertia\Response
    {
        return Inertia::render('Dashboard/MainSections/Create');
    }

    /**
     * Создает новый главный раздел
     */
    public function store(StoreMainSectionRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();

            $this->createMainSectionAction->run($validated);

            return redirect()->route('dashboard.main-sections.index')
                ->with('success', 'Главный раздел успешно создан!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Ошибка при создании главного раздела: ' . $e->getMessage());
        }
    }

    /**
     * Показывает форму редактирования раздела
     */
    public function edit(MainSection $mainSection): \Inertia\Response
    {
        $mainSection->load(['subSections']);

        return Inertia::render('Dashboard/MainSections/Edit', [
            'mainSection' => $mainSection,
        ]);
    }

    /**
     * Обновляет существующий главный раздел
     */
    public function update(UpdateMainSectionRequest $request, MainSection $mainSection): RedirectResponse
    {
        try {
            $validated = $request->validated();

            $this->updateMainSectionAction->run($mainSection, $validated);

            return redirect()->route('dashboard.main-sections.index')
                ->with('success', 'Главный раздел успешно обновлен!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении главного раздела: ' . $e->getMessage());
        }
    }

    /**
     * Удаляет главный раздел
     */
    public function destroy(MainSection $mainSection): RedirectResponse
    {
        try {
            $this->deleteMainSectionAction->run($mainSection);

            return redirect()->route('dashboard.main-sections.index')
                ->with('success', 'Главный раздел успешно удален!');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Ошибка при удалении главного раздела: ' . $e->getMessage());
        }
    }
}
