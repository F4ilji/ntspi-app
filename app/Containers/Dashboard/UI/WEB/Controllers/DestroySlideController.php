<?php

namespace App\Containers\Dashboard\UI\WEB\Controllers;

use App\Containers\Dashboard\Actions\Sliders\DeleteSlideAction;
use App\Containers\Widget\Models\Slide;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class DestroySlideController extends Controller
{
    public function __construct(
        private readonly DeleteSlideAction $deleteSlideAction,
    ) {}

    public function __invoke(Slide $slide): JsonResponse
    {
        $this->deleteSlideAction->run($slide);

        return response()->json([
            'success' => true,
        ]);
    }
}
