<?php

namespace App\Containers\Dashboard\UI\WEB\Controllers;

use App\Containers\AppStructure\Models\Page;
use App\Containers\AppStructure\Models\SubSection;
use App\Containers\Dashboard\Actions\Pages\AttachPageToSubSectionAction;
use App\Containers\Dashboard\Actions\Pages\DetachPageFromSubSectionAction;
use App\Containers\Dashboard\Actions\SubSections\AttachSubSectionToMainSectionAction;
use App\Containers\Dashboard\Actions\SubSections\CreateSubSectionAction;
use App\Containers\Dashboard\Actions\SubSections\DeleteSubSectionAction;
use App\Containers\Dashboard\Actions\SubSections\DetachSubSectionFromMainSectionAction;
use App\Containers\Dashboard\Actions\SubSections\ListSubSectionsAction;
use App\Containers\Dashboard\Actions\SubSections\UpdateSubSectionAction;
use App\Containers\Dashboard\UI\WEB\Requests\StoreSubSectionRequest;
use App\Containers\Dashboard\UI\WEB\Requests\UpdateSubSectionRequest;
use App\Containers\AppStructure\Models\MainSection;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SubSectionController extends Controller
{
    public function __construct(
        private readonly ListSubSectionsAction $listSubSectionsAction,
        private readonly CreateSubSectionAction $createSubSectionAction,
        private readonly UpdateSubSectionAction $updateSubSectionAction,
        private readonly DeleteSubSectionAction $deleteSubSectionAction,
        private readonly AttachSubSectionToMainSectionAction $attachSubSectionAction,
        private readonly DetachSubSectionFromMainSectionAction $detachSubSectionAction,
        private readonly AttachPageToSubSectionAction $attachPageAction,
        private readonly DetachPageFromSubSectionAction $detachPageAction,
    ) {}

    /**
     * Показывает список подразделов
     */
    public function index(Request $request): \Inertia\Response
    {
        $filters = $request->only(['search', 'main_section_id']);

        $data = $this->listSubSectionsAction->run($filters);
        $data['mainSections'] = MainSection::pluck('title', 'id');

        return Inertia::render('Dashboard/SubSections/Index', $data);
    }

    /**
     * Показывает форму создания подраздела
     */
    public function create(): \Inertia\Response
    {
        $mainSections = MainSection::pluck('title', 'id');

        return Inertia::render('Dashboard/SubSections/Create', [
            'mainSections' => $mainSections,
        ]);
    }

    /**
     * Создает новый подраздел
     */
    public function store(StoreSubSectionRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();

            $this->createSubSectionAction->run($validated);

            return redirect()->route('dashboard.sub-sections.index')
                ->with('success', 'Подраздел успешно создан!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Ошибка при создании подраздела: ' . $e->getMessage());
        }
    }

    /**
     * Показывает форму редактирования подраздела
     */
    public function edit(SubSection $subSection): \Inertia\Response
    {
        $subSection->load(['mainSection', 'pages']);

        $mainSections = MainSection::pluck('title', 'id');
        $availablePages = Page::whereNull('sub_section_id')
            ->whereNotNull('title')
            ->pluck('title', 'id');

        return Inertia::render('Dashboard/SubSections/Edit', [
            'subSection' => $subSection,
            'mainSections' => $mainSections,
            'availablePages' => $availablePages,
        ]);
    }

    /**
     * Обновляет существующий подраздел
     */
    public function update(UpdateSubSectionRequest $request, SubSection $subSection): RedirectResponse
    {
        try {
            $validated = $request->validated();

            $this->updateSubSectionAction->run($subSection, $validated);

            return redirect()->route('dashboard.sub-sections.index')
                ->with('success', 'Подраздел успешно обновлен!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении подраздела: ' . $e->getMessage());
        }
    }

    /**
     * Удаляет подраздел
     */
    public function destroy(SubSection $subSection): RedirectResponse
    {
        try {
            $this->deleteSubSectionAction->run($subSection);

            return redirect()->route('dashboard.sub-sections.index')
                ->with('success', 'Подраздел успешно удален!');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Ошибка при удалении подраздела: ' . $e->getMessage());
        }
    }

    /**
     * Прикрепляет подраздел к главному разделу
     */
    public function attachToMainSection(Request $request, SubSection $subSection): RedirectResponse
    {
        $request->validate([
            'main_section_id' => ['required', 'exists:main_sections,id'],
        ]);

        try {
            $mainSection = MainSection::findOrFail($request->main_section_id);
            $this->attachSubSectionAction->run($subSection, $mainSection);

            return back()->with('success', 'Подраздел прикреплен к главному разделу!');
        } catch (\Exception $e) {
            return back()->with('error', 'Ошибка при прикреплении подраздела: ' . $e->getMessage());
        }
    }

    /**
     * Открепляет подраздел от главного раздела
     */
    public function detachFromMainSection(SubSection $subSection): RedirectResponse
    {
        try {
            $this->detachSubSectionAction->run($subSection);

            return back()->with('success', 'Подраздел откреплен от главного раздела!');
        } catch (\Exception $e) {
            return back()->with('error', 'Ошибка при откреплении подраздела: ' . $e->getMessage());
        }
    }

    /**
     * Прикрепляет страницу к подразделу
     */
    public function attachPage(Request $request, SubSection $subSection): RedirectResponse
    {
        $request->validate([
            'page_id' => ['required', 'exists:pages,id'],
        ]);

        try {
            $page = Page::findOrFail($request->page_id);
            $this->attachPageAction->run($page, $subSection);

            return back()->with('success', 'Страница прикреплена к подразделу!');
        } catch (\Exception $e) {
            return back()->with('error', 'Ошибка при прикреплении страницы: ' . $e->getMessage());
        }
    }

    /**
     * Открепляет страницу от подраздела
     */
    public function detachPage(SubSection $subSection, Page $page): RedirectResponse
    {
        try {
            if ($page->sub_section_id !== $subSection->id) {
                abort(403, 'Страница не принадлежит этому подразделу');
            }

            $this->detachPageAction->run($page);

            return back()->with('success', 'Страница откреплена от подраздела!');
        } catch (\Exception $e) {
            return back()->with('error', 'Ошибка при откреплении страницы: ' . $e->getMessage());
        }
    }
}
