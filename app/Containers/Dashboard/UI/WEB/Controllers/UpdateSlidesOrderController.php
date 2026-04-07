<?php

namespace App\Containers\Dashboard\UI\WEB\Controllers;

use App\Containers\Dashboard\Actions\Sliders\UpdateSlidesOrderAction;
use App\Containers\Widget\Models\Slider;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UpdateSlidesOrderController extends Controller
{
    public function __construct(
        private readonly UpdateSlidesOrderAction $updateSlidesOrderAction,
    ) {}

    public function __invoke(Request $request, Slider $slider): JsonResponse
    {
        $request->validate([
            'slide_ids' => 'required|array',
            'slide_ids.*' => 'required|integer|exists:slides,id',
        ]);

        $this->updateSlidesOrderAction->run($slider, $request->input('slide_ids'));

        return response()->json([
            'success' => true,
        ]);
    }
}
