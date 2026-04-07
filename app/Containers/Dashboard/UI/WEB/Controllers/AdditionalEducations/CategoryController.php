<?php

namespace App\Containers\Dashboard\UI\WEB\Controllers\AdditionalEducations;

use App\Containers\AdditionalEducation\Models\AdditionalEducationCategory;
use App\Containers\Dashboard\Actions\AdditionalEducations\Categories\CreateCategoryAction;
use App\Containers\Dashboard\Actions\AdditionalEducations\Categories\DeleteCategoryAction;
use App\Containers\Dashboard\Actions\AdditionalEducations\Categories\ListCategoriesAction;
use App\Containers\Dashboard\Actions\AdditionalEducations\Categories\UpdateCategoryAction;
use App\Containers\Dashboard\UI\WEB\Requests\StoreCategoryRequest;
use App\Containers\Dashboard\UI\WEB\Requests\UpdateCategoryRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function __construct(
        private readonly ListCategoriesAction $listCategoriesAction,
        private readonly CreateCategoryAction $createCategoryAction,
        private readonly UpdateCategoryAction $updateCategoryAction,
        private readonly DeleteCategoryAction $deleteCategoryAction,
    ) {}

    public function index(\Illuminate\Http\Request $request): \Inertia\Response
    {
        $filters = $request->only(['search', 'direction_id', 'is_active']);
        $data = $this->listCategoriesAction->run($filters);

        return Inertia::render('Dashboard/AdditionalEducations/Categories/Index', $data);
    }

    public function create(): \Inertia\Response
    {
        $data = $this->listCategoriesAction->run([]);

        return Inertia::render('Dashboard/AdditionalEducations/Categories/Create', [
            'directions' => $data['directions'],
        ]);
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();
            $this->createCategoryAction->run($validated);

            return redirect()->route('dashboard.additional-educations.categories.index')
                ->with('success', 'Категория дополнительного образования успешно создана!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Ошибка при создании категории: ' . $e->getMessage());
        }
    }

    public function edit(AdditionalEducationCategory $category): \Inertia\Response
    {
        $category->load(['direction']);
        $data = $this->listCategoriesAction->run([]);

        return Inertia::render('Dashboard/AdditionalEducations/Categories/Edit', [
            'category' => $category,
            'directions' => $data['directions'],
        ]);
    }

    public function update(UpdateCategoryRequest $request, AdditionalEducationCategory $category): RedirectResponse
    {
        try {
            $validated = $request->validated();
            $this->updateCategoryAction->run($category, $validated);

            return redirect()->route('dashboard.additional-educations.categories.index')
                ->with('success', 'Категория успешно обновлена!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Ошибка при обновлении категории: ' . $e->getMessage());
        }
    }

    public function destroy(AdditionalEducationCategory $category): RedirectResponse
    {
        try {
            $this->deleteCategoryAction->run($category);

            return redirect()->route('dashboard.additional-educations.categories.index')
                ->with('success', 'Категория успешно удалена!');
        } catch (\Exception $e) {
            return back()->with('error', 'Ошибка при удалении категории: ' . $e->getMessage());
        }
    }
}
