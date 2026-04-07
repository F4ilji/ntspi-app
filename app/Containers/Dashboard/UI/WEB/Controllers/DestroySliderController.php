<?php

namespace App\Containers\Dashboard\UI\WEB\Controllers;

use App\Containers\Dashboard\Actions\Sliders\DeleteSliderAction;
use App\Containers\Widget\Models\Slider;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class DestroySliderController extends Controller
{
    public function __construct(
        private readonly DeleteSliderAction $deleteSliderAction,
    ) {}

    public function __invoke(Slider $slider): RedirectResponse
    {
        $this->deleteSliderAction->run($slider);

        return redirect()->route('dashboard.sliders.index')
            ->with('success', 'Слайдер удален');
    }
}
