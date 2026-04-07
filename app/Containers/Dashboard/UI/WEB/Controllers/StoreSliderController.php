<?php

namespace App\Containers\Dashboard\UI\WEB\Controllers;

use App\Containers\Dashboard\Actions\Sliders\CreateSliderAction;
use App\Containers\Dashboard\UI\WEB\Requests\StoreSliderRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class StoreSliderController extends Controller
{
    public function __construct(
        private readonly CreateSliderAction $createSliderAction,
    ) {}

    public function __invoke(StoreSliderRequest $request): RedirectResponse
    {
        $slider = $this->createSliderAction->run($request->validated());

        return redirect()->route('dashboard.sliders.edit', $slider->id)
            ->with('success', 'Слайдер успешно создан');
    }
}
