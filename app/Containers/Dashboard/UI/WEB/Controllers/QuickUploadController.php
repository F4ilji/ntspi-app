<?php

namespace App\Containers\Dashboard\UI\WEB\Controllers;

use App\Containers\Dashboard\Actions\Posts\QuickUploadFileAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class QuickUploadController extends Controller
{
    public function create(): \Inertia\Response
    {
        return Inertia::render('Dashboard/QuickUpload');
    }

    public function store(Request $request, QuickUploadFileAction $quickUploadFileAction): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'max:20000'],
        ]);

        try {
            $result = $quickUploadFileAction->run($request->file('file'));

            return response()->json([
                'success' => true,
                'data' => [
                    'url' => url($result['url']),
                    'path' => $result['path'],
                    'original_name' => $result['original_name'],
                ],
            ]);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Ошибка при загрузке файла: ' . $e->getMessage(),
            ], 500);
        }
    }
}
