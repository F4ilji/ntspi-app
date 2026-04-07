<?php

namespace App\Containers\Dashboard\UI\WEB\Controllers;

use App\Containers\Education\Models\DirectionStudy;
use App\Containers\Dashboard\Actions\DirectionStudies\CreateDirectionStudyAction;
use App\Containers\Dashboard\Actions\DirectionStudies\DeleteDirectionStudyAction;
use App\Containers\Dashboard\Actions\DirectionStudies\ListDirectionStudiesAction;
use App\Containers\Dashboard\Actions\DirectionStudies\UpdateDirectionStudyAction;
use App\Containers\Dashboard\UI\WEB\Requests\StoreDirectionStudyRequest;
use App\Containers\Dashboard\UI\WEB\Requests\UpdateDirectionStudyRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DirectionStudyController extends Controller
{
    public function __construct(
        private readonly ListDirectionStudiesAction $listDirectionStudiesAction,
        private readonly CreateDirectionStudyAction $createDirectionStudyAction,
        private readonly UpdateDirectionStudyAction $updateDirectionStudyAction,
        private readonly DeleteDirectionStudyAction $deleteDirectionStudyAction,
    ) {}

    public function index(Request $request): \Inertia\Response
    {
        $filters = $request->only(['search', 'lvl_edu']);
        $data = $this->listDirectionStudiesAction->run($filters);

        return Inertia::render('Dashboard/DirectionStudies/Index', $data);
    }

    public function create(): \Inertia\Response
    {
        $data = $this->listDirectionStudiesAction->run([]);

        return Inertia::render('Dashboard/DirectionStudies/Create', [
            'educationLevels' => $data['educationLevels'],
        ]);
    }

    public function store(StoreDirectionStudyRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();
            $this->createDirectionStudyAction->run($validated);

            return redirect()->route('dashboard.direction-studies.index')
                ->with('success', 'Направление подготовки успешно создано!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Ошибка при создании направления: ' . $e->getMessage());
        }
    }

    public function edit(DirectionStudy $directionStudy): \Inertia\Response
    {
        $data = $this->listDirectionStudiesAction->run([]);

        return Inertia::render('Dashboard/DirectionStudies/Edit', [
            'direction' => $directionStudy,
            'educationLevels' => $data['educationLevels'],
        ]);
    }

    public function update(UpdateDirectionStudyRequest $request, DirectionStudy $directionStudy): RedirectResponse
    {
        try {
            $validated = $request->validated();
            $this->updateDirectionStudyAction->run($directionStudy, $validated);

            return redirect()->route('dashboard.direction-studies.index')
                ->with('success', 'Направление подготовки успешно обновлено!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Ошибка при обновлении направления: ' . $e->getMessage());
        }
    }

    public function destroy(DirectionStudy $directionStudy): RedirectResponse
    {
        try {
            $this->deleteDirectionStudyAction->run($directionStudy);

            return redirect()->route('dashboard.direction-studies.index')
                ->with('success', 'Направление подготовки успешно удалено!');
        } catch (\Exception $e) {
            return back()->with('error', 'Ошибка при удалении направления: ' . $e->getMessage());
        }
    }
}
