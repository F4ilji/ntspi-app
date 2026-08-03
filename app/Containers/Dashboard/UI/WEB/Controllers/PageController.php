<?php

namespace App\Containers\Dashboard\UI\WEB\Controllers;

use App\Containers\AppStructure\Models\Page;
use App\Containers\Dashboard\Actions\ContentBuilder\UploadContentBuilderFilesAction;
use App\Containers\Dashboard\Actions\Pages\CreatePageAction;
use App\Containers\Dashboard\Actions\Pages\DeletePageAction;
use App\Containers\Dashboard\Actions\Pages\ExportPageAction;
use App\Containers\Dashboard\Actions\Pages\ImportPageAction;
use App\Containers\Dashboard\Actions\Pages\ListPagesAction;
use App\Containers\Dashboard\Actions\Pages\UpdatePageAction;
use App\Containers\Dashboard\UI\WEB\Requests\StorePageRequest;
use App\Containers\Dashboard\UI\WEB\Requests\UpdatePageRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

class PageController extends Controller
{
    public function __construct(
        private readonly ListPagesAction $listPagesAction,
        private readonly CreatePageAction $createPageAction,
        private readonly UpdatePageAction $updatePageAction,
        private readonly DeletePageAction $deletePageAction,
        private readonly ExportPageAction $exportPageAction,
        private readonly ImportPageAction $importPageAction,
        private readonly UploadContentBuilderFilesAction $uploadContentBuilderFilesAction,
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

    /**
     * Экспортирует страницу в JSON-файл
     */
    public function export(Page $page): Response
    {
        try {
            $data = $this->exportPageAction->run($page);

            $filename = $page->slug . '_' . now()->format('Y-m-d_H-i-s') . '.json';

            return response(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), 200, [
                'Content-Type' => 'application/json',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Ошибка при экспорте страницы: ' . $e->getMessage());
        }
    }

    /**
     * Импортирует страницу из JSON-файла
     */
    public function import(Request $request): RedirectResponse
    {
        if (config('app.env') !== 'local') {
            return back()->with('error', 'Импорт доступен только в окружении local');
        }

        try {
            $request->validate([
                'import_file' => 'required|file|mimes:json,txt|max:512',
            ]);

            $file = $request->file('import_file');
            $content = file_get_contents($file->getRealPath());
            $data = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->with('error', 'Некорректный JSON: ' . json_last_error_msg());
            }

            $page = $this->importPageAction->run($data);

            return redirect()->route('dashboard.pages.edit', $page)
                ->with('success', 'Страница "' . $page->title . '" успешно импортирована!');
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', 'Ошибка валидации: ' . $e->getMessage());
        } catch (\Exception $e) {
            return back()->with('error', 'Ошибка при импорте страницы: ' . $e->getMessage());
        }
    }

    /**
     * Загружает файлы для ContentBuilder и возвращает метаданные
     */
    public function uploadFiles(Request $request): JsonResponse
    {
        $request->validate([
            'files' => 'required|array',
            'files.*' => 'required|file|mimes:pdf,docx,xlsx,pptx,zip,doc,xls,ppt|max:524288',
        ]);

        try {
            $results = $this->uploadContentBuilderFilesAction->run($request->file('files'));

            return response()->json([
                'success' => true,
                'files' => $results,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Ошибка при загрузке файлов: ' . $e->getMessage(),
            ], 500);
        }
    }
}
