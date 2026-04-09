<?php

namespace App\Containers\Dashboard\UI\WEB\Controllers;

use App\Containers\Dashboard\Actions\Posts\DeploySiteAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DeployController extends Controller
{
    public function __construct(
        private readonly DeploySiteAction $deploySiteAction,
    ) {}

    /**
     * Запускает деплой (создаёт файл-триггер)
     */
    public function deploy(Request $request): JsonResponse
    {
        if (app()->environment() !== 'production') {
            return response()->json([
                'success' => false,
                'message' => 'Деплой доступен только на production',
            ], 403);
        }

        if (!$request->user()->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Недостаточно прав для выполнения этой операции',
            ], 403);
        }

        $result = $this->deploySiteAction->run();

        return response()->json($result);
    }

    /**
     * Проверяет статус деплоя
     */
    public function status(Request $request): JsonResponse
    {
        if (app()->environment() !== 'production') {
            return response()->json(['status' => 'disabled'], 403);
        }

        if (!$request->user()->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Недостаточно прав',
            ], 403);
        }

        $status = $this->deploySiteAction->getStatus();

        return response()->json($status);
    }

    /**
     * Очищает статус деплоя
     */
    public function clear(Request $request): JsonResponse
    {
        if (!$request->user()->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Недостаточно прав',
            ], 403);
        }

        $this->deploySiteAction->clearStatus();

        return response()->json(['success' => true]);
    }
}
