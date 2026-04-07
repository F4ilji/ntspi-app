<?php

namespace App\Containers\Dashboard\UI\WEB\Controllers;

use App\Containers\Dashboard\Actions\PageReferenceLists\CreatePageReferenceListAction;
use App\Containers\Dashboard\Actions\PageReferenceLists\DeletePageReferenceListAction;
use App\Containers\Dashboard\Actions\PageReferenceLists\ListPageReferenceListsAction;
use App\Containers\Dashboard\Actions\PageReferenceLists\UpdatePageReferenceListAction;
use App\Containers\Dashboard\UI\WEB\Requests\StorePageReferenceListRequest;
use App\Containers\Dashboard\UI\WEB\Requests\UpdatePageReferenceListRequest;
use App\Containers\Widget\Models\PageReferenceList;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PageReferenceListController extends Controller
{
    public function __construct(
        private readonly ListPageReferenceListsAction $listPageReferenceListsAction,
        private readonly CreatePageReferenceListAction $createPageReferenceListAction,
        private readonly UpdatePageReferenceListAction $updatePageReferenceListAction,
        private readonly DeletePageReferenceListAction $deletePageReferenceListAction,
    ) {}

    public function index(Request $request): \Inertia\Response
    {
        $filters = $request->only(['search', 'is_active']);
        $data = $this->listPageReferenceListsAction->run($filters);

        return Inertia::render('Dashboard/PageReferenceLists/Index', $data);
    }

    public function create(): \Inertia\Response
    {
        return Inertia::render('Dashboard/PageReferenceLists/Create');
    }

    public function store(StorePageReferenceListRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();
            $this->createPageReferenceListAction->run($validated);

            return redirect()->route('dashboard.page-reference-lists.index')
                ->with('success', 'Список ресурсов успешно создан!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Ошибка при создании: ' . $e->getMessage());
        }
    }

    public function edit(PageReferenceList $pageReferenceList): \Inertia\Response
    {
        return Inertia::render('Dashboard/PageReferenceLists/Edit', [
            'list' => $pageReferenceList,
        ]);
    }

    public function update(UpdatePageReferenceListRequest $request, PageReferenceList $pageReferenceList): RedirectResponse
    {
        try {
            $validated = $request->validated();
            $this->updatePageReferenceListAction->run($pageReferenceList, $validated);

            return redirect()->route('dashboard.page-reference-lists.index')
                ->with('success', 'Список ресурсов успешно обновлён!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Ошибка при обновлении: ' . $e->getMessage());
        }
    }

    public function destroy(PageReferenceList $pageReferenceList): RedirectResponse
    {
        try {
            $this->deletePageReferenceListAction->run($pageReferenceList);

            return redirect()->route('dashboard.page-reference-lists.index')
                ->with('success', 'Список ресурсов успешно удалён!');
        } catch (\Exception $e) {
            return back()->with('error', 'Ошибка при удалении: ' . $e->getMessage());
        }
    }
}
