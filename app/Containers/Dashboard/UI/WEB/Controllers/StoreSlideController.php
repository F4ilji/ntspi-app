<?php

namespace App\Containers\Dashboard\UI\WEB\Controllers;

use App\Containers\Dashboard\Actions\Sliders\CreateSlideAction;
use App\Containers\Widget\Models\Slider;
use App\Containers\Dashboard\UI\WEB\Requests\StoreSlideRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class StoreSlideController extends Controller
{
    public function __construct(
        private readonly CreateSlideAction $createSlideAction,
    ) {}

    public function __invoke(StoreSlideRequest $request, Slider $slider): JsonResponse
    {
        $slide = $this->createSlideAction->run($slider, $request->validated());

        return response()->json([
            'success' => true,
            'slide' => $slide,
        ]);
    }
}
