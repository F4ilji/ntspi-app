<?php

namespace App\Containers\Dashboard\UI\WEB\Controllers;

use App\Containers\Dashboard\Actions\ProcessMixedFilesAction;
use App\Containers\Dashboard\UI\WEB\Requests\StoreFilesRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;

class ProcessMixedFilesController extends Controller
{
    public function __construct(
        private readonly ProcessMixedFilesAction $processMixedFilesAction,
    ) {}

    /**
     * Обработка смешанных файлов и создание новости
     */
    public function __invoke(StoreFilesRequest $request): RedirectResponse
    {
        try {
            // Собираем все файлы в одну коллекцию
            $files = $this->collectAllFiles($request);

            if ($files->isEmpty()) {
                return back()->with(['error' => 'Не загружено ни одного файла']);
            }

            $result = $this->processMixedFilesAction->run($files);

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

    /**
     * Собирает все файлы из request
     */
    private function collectAllFiles(StoreFilesRequest $request): Collection
    {
        $files = collect();

        // Добавляем файлы из поля 'files' (множественная загрузка)
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                if ($file instanceof Collection) {
                    $files = $files->merge($file);
                } else {
                    $files->push($file);
                }
            }
        }

        return $files;
    }
}
