<?php

namespace App\Containers\Dashboard\UI\WEB\Controllers;

use App\Containers\Dashboard\Actions\Sliders\UpdateSliderAction;
use App\Containers\Dashboard\UI\WEB\Requests\UpdateSliderRequest;
use App\Containers\Widget\Models\Slider;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class UpdateSliderController extends Controller
{
    public function __construct(
        private readonly UpdateSliderAction $updateSliderAction,
    ) {}

    public function __invoke(UpdateSliderRequest $request, Slider $slider): RedirectResponse
    {
        $this->updateSliderAction->run($slider, $request->validated());

        return redirect()->back()
            ->with('success', 'Слайдер успешно обновлен');
    }
}
