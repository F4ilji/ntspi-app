<?php

namespace App\Containers\Dashboard\UI\WEB\Controllers;

use App\Containers\Dashboard\Actions\Sveden\UpdateSvedenAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SvedenController extends Controller
{
    public function __construct(
        private readonly UpdateSvedenAction $updateAction,
    ) {}

    public function index(): \Inertia\Response
    {
        return Inertia::render('Dashboard/Sveden');
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'archive' => ['required', 'file', 'mimes:zip', 'max:102400'],
        ]);

        try {
            $result = $this->updateAction->run($request->file('archive'));

            return response()->json($result);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Ошибка при обновлении: ' . $e->getMessage(),
            ], 500);
        }
    }
}
