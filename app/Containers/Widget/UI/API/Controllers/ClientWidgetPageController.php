<?php

namespace App\Containers\Widget\UI\API\Controllers;

use App\Containers\Widget\Actions\GetPageAction;
use App\Ship\Controllers\Controller;

class ClientWidgetPageController extends Controller
{
    public function __construct(
        private readonly GetPageAction $getPageAction
    ) {}

    public function single(int $id): \Illuminate\Http\JsonResponse
    {
        $data = $this->getPageAction->run($id);

        return response()->json(['data' => $data], 200);
    }
}
