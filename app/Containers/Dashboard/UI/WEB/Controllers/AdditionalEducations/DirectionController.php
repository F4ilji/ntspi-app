<?php

namespace App\Containers\Dashboard\UI\WEB\Controllers\AdditionalEducations;

use App\Containers\AdditionalEducation\Models\DirectionAdditionalEducation;
use App\Containers\Dashboard\Actions\AdditionalEducations\Directions\CreateDirectionAction;
use App\Containers\Dashboard\Actions\AdditionalEducations\Directions\DeleteDirectionAction;
use App\Containers\Dashboard\Actions\AdditionalEducations\Directions\ListDirectionsAction;
use App\Containers\Dashboard\Actions\AdditionalEducations\Directions\UpdateDirectionAction;
use App\Containers\Dashboard\UI\WEB\Requests\StoreDirectionRequest;
use App\Containers\Dashboard\UI\WEB\Requests\UpdateDirectionRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DirectionController extends Controller
{
    public function __construct(
        private readonly ListDirectionsAction $listDirectionsAction,
        private readonly CreateDirectionAction $createDirectionAction,
        private readonly UpdateDirectionAction $updateDirectionAction,
        private readonly DeleteDirectionAction $deleteDirectionAction,
    ) {}

    public function index(Request $request): \Inertia\Response
    {
        $filters = $request->only(['search', 'is_active']);
        $data = $this->listDirectionsAction->run($filters);

        return Inertia::render('Dashboard/AdditionalEducations/Directions/Index', $data);
    }

    public function create(): \Inertia\Response
    {
        return Inertia::render('Dashboard/AdditionalEducations/Directions/Create');
    }

    public function store(StoreDirectionRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();
            $this->createDirectionAction->run($validated);

            return redirect()->route('dashboard.additional-educations.directions.index')
                ->with('success', 'Направление дополнительного образования успешно создано!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Ошибка при создании направления: ' . $e->getMessage());
        }
    }

    public function edit(DirectionAdditionalEducation $direction): \Inertia\Response
    {
        return Inertia::render('Dashboard/AdditionalEducations/Directions/Edit', [
            'direction' => $direction,
        ]);
    }

    public function update(UpdateDirectionRequest $request, DirectionAdditionalEducation $direction): RedirectResponse
    {
        try {
            $validated = $request->validated();
            $this->updateDirectionAction->run($direction, $validated);

            return redirect()->route('dashboard.additional-educations.directions.index')
                ->with('success', 'Направление успешно обновлено!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Ошибка при обновлении направления: ' . $e->getMessage());
        }
    }

    public function destroy(DirectionAdditionalEducation $direction): RedirectResponse
    {
        try {
            $this->deleteDirectionAction->run($direction);

            return redirect()->route('dashboard.additional-educations.directions.index')
                ->with('success', 'Направление успешно удалено!');
        } catch (\Exception $e) {
            return back()->with('error', 'Ошибка при удалении направления: ' . $e->getMessage());
        }
    }
}
