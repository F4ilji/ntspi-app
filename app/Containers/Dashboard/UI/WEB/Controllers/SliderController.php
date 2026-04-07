<?php

namespace App\Containers\Dashboard\UI\WEB\Controllers;

use App\Containers\Dashboard\Actions\Sliders\ListSlidersAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SliderController extends Controller
{
    public function __construct(
        private readonly ListSlidersAction $listSlidersAction,
    ) {}

    public function index(Request $request): \Inertia\Response
    {
        $filters = $request->only(['search', 'is_active']);
        $sliders = $this->listSlidersAction->run($filters);

        return inertia()->render('Dashboard/Sliders/Index', [
            'sliders' => $sliders,
            'filters' => $filters,
        ]);
    }
}
