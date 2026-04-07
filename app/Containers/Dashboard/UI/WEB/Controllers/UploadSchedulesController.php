<?php

namespace App\Containers\Dashboard\UI\WEB\Controllers;

use App\Containers\Dashboard\Actions\Schedules\UploadMultipleSchedulesAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UploadSchedulesController extends Controller
{
    public function __construct(
        private readonly UploadMultipleSchedulesAction $uploadMultipleSchedulesAction,
    ) {}

    /**
     * Показывает страницу загрузки расписаний
     */
    public function create(): \Inertia\Response
    {
        return Inertia::render('Dashboard/Schedules/Upload');
    }

    /**
     * Обрабатывает загрузку файлов
     */
    public function store(Request $request): \Inertia\Response
    {
        // Получаем файлы из request (FormData с files[])
        $files = $request->file('files', []);
        
        // Если files - это один файл (не массив), преобразуем в массив
        if (!is_array($files)) {
            $files = [$files];
        }

        // Валидация каждого файла
        $validator = \Illuminate\Support\Facades\Validator::make(
            ['files' => $files],
            [
                'files' => 'required|array',
                'files.*' => 'required|file|mimes:pdf|max:10000',
            ],
            [
                'files.required' => 'Выберите файлы для загрузки',
                'files.*.mimes' => 'Разрешены только PDF файлы',
                'files.*.max' => 'Размер файла не должен превышать 10MB',
            ]
        );

        if ($validator->fails()) {
            return Inertia::render('Dashboard/Schedules/Upload', [
                'flash' => [
                    'message' => $validator->errors()->first(),
                    'type' => 'error',
                ],
            ])->withInput();
        }

        if (empty($files)) {
            return Inertia::render('Dashboard/Schedules/Upload', [
                'flash' => [
                    'message' => 'Нет файлов для обработки',
                    'type' => 'error',
                ],
            ]);
        }

        $result = $this->uploadMultipleSchedulesAction->run($files);

        $hasProcessed = $result['processed_count'] > 0;
        $hasFailed = $result['failed_count'] > 0;

        $message = '';
        $messageType = 'success';

        if ($hasProcessed && $hasFailed) {
            $message = "Успешно: {$result['processed_count']}. Ошибки: {$result['failed_count']}.";
            $messageType = 'warning';
        } elseif ($hasProcessed) {
            $message = "Успешно загружено: {$result['processed_count']} файл(ов)";
            $messageType = 'success';
        } elseif ($hasFailed) {
            $message = "Ошибки при обработке: {$result['failed_count']} файл(ов)";
            $messageType = 'error';
        }

        return Inertia::render('Dashboard/Schedules/Upload', [
            'flash' => [
                'message' => $message,
                'type' => $messageType,
            ],
            'data' => $result,
        ]);
    }
}
