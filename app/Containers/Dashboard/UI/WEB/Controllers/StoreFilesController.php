<?php

namespace App\Containers\Dashboard\UI\WEB\Controllers;

use App\Containers\Dashboard\Actions\EmailNews\ProcessUploadedFilesAction;
use App\Containers\Dashboard\UI\WEB\Requests\StoreFilesRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class StoreFilesController extends Controller
{
    public function __construct(
        private readonly ProcessUploadedFilesAction $processUploadedFilesAction,
    ) {}

    /**
     * Обработка загруженных файлов и создание новости
     */
    public function __invoke(StoreFilesRequest $request): RedirectResponse
    {
        try {
            $result = $this->processUploadedFilesAction->run(
                $request->file('document'),
                $request->file('media', [])
            );

            return back()->with([
                'success' => 'Файлы успешно загружены! Новость создана: ' . $result['post']->title,
                'extracted_text' => $result['newsData'],
                'created_post' => [
                    ...$result['post']->toArray(),
                    'preview_url' => $result['preview_url'],
                    'images_urls' => $result['images_urls'],
                ],
            ]);
        } catch (\Exception $e) {
            return back()->with([
                'error' => 'Ошибка при создании новости: ' . $e->getMessage(),
            ]);
        }
    }
}
