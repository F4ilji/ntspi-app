<?php

namespace App\Containers\Dashboard\UI\WEB\Controllers;

use App\Containers\Dashboard\Actions\Posts\DeploySiteAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;

class DeployController extends Controller
{
    public function __construct(
        private readonly DeploySiteAction $deploySiteAction,
    ) {}

    public function index(): \Inertia\Response
    {
        $history = $this->deploySiteAction->getHistory();
        $status = $this->deploySiteAction->getStatus();

        return Inertia::render('Dashboard/Deploy/Index', [
            'history' => $history['history'],
            'status' => $status,
        ]);
    }

    public function deploy(Request $request): JsonResponse
    {
        if (app()->environment() !== 'production') {
            return response()->json([
                'success' => false,
                'message' => 'Деплой доступен только на production',
            ], 403);
        }

        if (!$request->user()->hasRole('super_admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Недостаточно прав для выполнения этой операции',
            ], 403);
        }

        $result = $this->deploySiteAction->run();

        return response()->json($result);
    }

    public function status(Request $request): JsonResponse
    {
        if (app()->environment() !== 'production') {
            return response()->json(['status' => 'disabled'], 403);
        }

        if (!$request->user()->hasRole('super_admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Недостаточно прав',
            ], 403);
        }

        $status = $this->deploySiteAction->getStatus();

        return response()->json($status);
    }

    public function log(Request $request): JsonResponse
    {
        if (!$request->user()->hasRole('super_admin')) {
            return response()->json(['success' => false], 403);
        }

        $lines = (int) $request->query('lines', 50);
        $result = $this->deploySiteAction->getLog($lines);

        return response()->json($result);
    }

    public function history(Request $request): JsonResponse
    {
        if (!$request->user()->hasRole('super_admin')) {
            return response()->json(['success' => false], 403);
        }

        $result = $this->deploySiteAction->getHistory();

        return response()->json($result);
    }

    public function clear(Request $request): JsonResponse
    {
        if (!$request->user()->hasRole('super_admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Недостаточно прав',
            ], 403);
        }

        $this->deploySiteAction->clearStatus();

        return response()->json(['success' => true]);
    }
}
