<?php

namespace App\Containers\Dashboard\UI\WEB\Controllers;

use App\Containers\Dashboard\Actions\Sliders\UpdateSlideAction;
use App\Containers\Widget\Models\Slide;
use App\Containers\Dashboard\UI\WEB\Requests\UpdateSlideRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class UpdateSlideController extends Controller
{
    public function __construct(
        private readonly UpdateSlideAction $updateSlideAction,
    ) {}

    public function __invoke(UpdateSlideRequest $request, Slide $slide): JsonResponse
    {
        $slide = $this->updateSlideAction->run($slide, $request->validated());

        return response()->json([
            'success' => true,
            'slide' => $slide,
        ]);
    }
}
