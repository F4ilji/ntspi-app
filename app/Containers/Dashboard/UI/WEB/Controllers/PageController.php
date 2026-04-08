<?php

namespace App\Containers\Dashboard\UI\WEB\Controllers;

use App\Containers\AppStructure\Models\Page;
use App\Containers\Dashboard\Actions\Pages\CreatePageAction;
use App\Containers\Dashboard\Actions\Pages\DeletePageAction;
use App\Containers\Dashboard\Actions\Pages\ListPagesAction;
use App\Containers\Dashboard\Actions\Pages\UpdatePageAction;
use App\Containers\Dashboard\UI\WEB\Requests\StorePageRequest;
use App\Containers\Dashboard\UI\WEB\Requests\UpdatePageRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PageController extends Controller
{
    public function __construct(
        private readonly ListPagesAction $listPagesAction,
        private readonly CreatePageAction $createPageAction,
        private readonly UpdatePageAction $updatePageAction,
        private readonly DeletePageAction $deletePageAction,
    ) {}

    /**
     * Показывает список страниц
     */
    public function index(Request $request): \Inertia\Response
    {
        $filters = $request->only(['search', 'tab', 'sub_section_id']);

        $data = $this->listPagesAction->run($filters);

        return Inertia::render('Dashboard/Pages/Index', $data);
    }

    /**
     * Показывает форму создания страницы
     */
    public function create(): \Inertia\Response
    {
        $data = $this->listPagesAction->run([]);

        return Inertia::render('Dashboard/Pages/Create', [
            'subSections' => $data['subSections'],
        ]);
    }

    /**
     * Создает новую страницу
     */
    public function store(StorePageRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();

            $this->createPageAction->run($validated);

            return redirect()->route('dashboard.pages.index')
                ->with('success', 'Страница успешно создана!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Ошибка при создании страницы: ' . $e->getMessage());
        }
    }

    /**
     * Показывает форму редактирования страницы
     */
    public function edit(Page $page): \Inertia\Response
    {
        $page->load(['section.mainSection']);

        $data = $this->listPagesAction->run([]);

        return Inertia::render('Dashboard/Pages/Edit', [
            'page' => $page,
            'subSections' => $data['subSections'],
        ]);
    }

    /**
     * Обновляет существующую страницу
     */
    public function update(UpdatePageRequest $request, Page $page): RedirectResponse
    {
        try {
            $validated = $request->validated();

            $this->updatePageAction->run($page, $validated);

            return redirect()->route('dashboard.pages.edit', $page)
                ->with('success', 'Страница успешно обновлена!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении страницы: ' . $e->getMessage());
        }
    }

    /**
     * Удаляет страницу
     */
    public function destroy(Page $page): RedirectResponse
    {
        try {
            $this->deletePageAction->run($page);

            return redirect()->route('dashboard.pages.index')
                ->with('success', 'Страница успешно удалена!');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Ошибка при удалении страницы: ' . $e->getMessage());
        }
    }
}
