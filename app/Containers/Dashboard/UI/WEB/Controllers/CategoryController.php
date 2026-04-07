<?php

namespace App\Containers\Dashboard\UI\WEB\Controllers;

use App\Containers\Article\Models\Category;
use App\Containers\Dashboard\Actions\Categories\CreateCategoryAction;
use App\Containers\Dashboard\Actions\Categories\DeleteCategoryAction;
use App\Containers\Dashboard\Actions\Categories\ListCategoriesAction;
use App\Containers\Dashboard\Actions\Categories\UpdateCategoryAction;
use App\Containers\Dashboard\UI\WEB\Requests\StoreNewsCategoryRequest;
use App\Containers\Dashboard\UI\WEB\Requests\UpdateNewsCategoryRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function __construct(
        private readonly ListCategoriesAction $listCategoriesAction,
        private readonly CreateCategoryAction $createCategoryAction,
        private readonly UpdateCategoryAction $updateCategoryAction,
        private readonly DeleteCategoryAction $deleteCategoryAction,
    ) {}

    public function index(Request $request): \Inertia\Response
    {
        $filters = $request->only(['search', 'is_active']);

        $data = $this->listCategoriesAction->run($filters);

        return Inertia::render('Dashboard/Categories/Index', $data);
    }

    public function create(): \Inertia\Response
    {
        return Inertia::render('Dashboard/Categories/Create');
    }

    public function store(StoreNewsCategoryRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();

            $this->createCategoryAction->run($validated);

            return redirect()->route('dashboard.categories.index')
                ->with('success', 'Категория успешно создана!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Ошибка при создании категории: ' . $e->getMessage());
        }
    }

    public function edit(Category $category): \Inertia\Response
    {
        return Inertia::render('Dashboard/Categories/Edit', [
            'category' => $category,
        ]);
    }

    public function update(UpdateNewsCategoryRequest $request, Category $category): RedirectResponse
    {
        try {
            $validated = $request->validated();

            $this->updateCategoryAction->run($category, $validated);

            return redirect()->route('dashboard.categories.index')
                ->with('success', 'Категория успешно обновлена!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении категории: ' . $e->getMessage());
        }
    }

    public function destroy(Category $category): RedirectResponse
    {
        try {
            $this->deleteCategoryAction->run($category);

            return redirect()->route('dashboard.categories.index')
                ->with('success', 'Категория успешно удалена!');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Ошибка при удалении категории: ' . $e->getMessage());
        }
    }
}
