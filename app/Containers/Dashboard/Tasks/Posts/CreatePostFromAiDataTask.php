<?php

namespace App\Containers\Dashboard\Tasks\Posts;

use App\Containers\Article\Enums\PostStatus;
use App\Containers\Article\Models\Post;
use App\Services\Filament\Domain\Posts\PostDataProcessor;
use Illuminate\Support\Str;

class CreatePostFromAiDataTask
{
    /**
     * Создаёт пост из данных, полученных от AI
     *
     * @param array $newsData Данные от AI (title, body, authors, tags, category_id)
     * @param string|null $documentPath Путь к основному документу
     * @param array $mediaPaths Пути к медиафайлам
     * @param array $attachedFiles Прикреплённые файлы (для блока files)
     */
    public function run(array $newsData, ?string $documentPath, array $mediaPaths, array $attachedFiles): Post
    {
        // Формируем контент в формате редактора
        $content = $this->buildContent($newsData, $attachedFiles);

        // Обрабатываем изображения
        ['preview' => $previewPath, 'images' => $imagesPaths] = $this->processImages($mediaPaths);

        // Подготавливаем данные для создания поста
        $postData = $this->preparePostData($newsData, $content, $previewPath, $imagesPaths);

        // Обрабатываем через PostDataProcessor (как в админке)
        $processedData = (new PostDataProcessor())->processCreate($postData);

        // Создаём пост
        $post = Post::create($processedData);

        // Сохраняем теги
        $this->syncTags($post, $newsData);

        return $post;
    }

    /**
     * Формирует контент в формате редактора
     */
    private function buildContent(array $newsData, array $attachedFiles): array
    {
        $content = [
            [
                'type' => 'paragraph',
                'data' => [
                    'seo_active' => true,
                    'content' => $newsData['body'] ?? '',
                ],
            ],
        ];

        if (!empty($attachedFiles)) {
            $content[] = [
                'type' => 'files',
                'data' => [
                    'file' => $attachedFiles,
                ],
            ];
        }

        return $content;
    }

    /**
     * Обрабатывает изображения: все изображения идут в gallery, первое — preview
     */
    private function processImages(array $mediaPaths): array
    {
        $imagesPaths = [];

        $imageExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp', 'svg', 'tiff'];

        foreach ($mediaPaths as $path) {
            $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

            if (in_array($extension, $imageExtensions)) {
                $imagesPaths[] = $path;
            }
        }

        return [
            'preview' => !empty($imagesPaths) ? reset($imagesPaths) : null,
            'images' => $imagesPaths,
        ];
    }

    /**
     * Подготавливает данные для создания поста
     */
    private function preparePostData(array $newsData, array $content, ?string $previewPath, array $imagesPaths): array
    {
        $baseSlug = Str::slug($newsData['title'] ?? '');
        $slug = $this->generateUniqueSlug($baseSlug);

        return [
            'title' => $newsData['title'] ?? 'Без названия',
            'slug' => $slug,
            'content' => $content,
            'authors' => $newsData['authors'] ?? [],
            'category_id' => $newsData['category_id'] ?? null,
            'status' => PostStatus::VERIFICATION,
            'user_id' => auth()->id(),
            'preview' => $previewPath,
            'images' => !empty($imagesPaths) ? $imagesPaths : null,
            'publish_setting' => [
                'publish_after' => false,
                'publish_at' => null,
            ],
            'publication' => [],
        ];
    }

    /**
     * Генерирует уникальный slug
     */
    private function generateUniqueSlug(string $baseSlug): string
    {
        $slug = $baseSlug;
        $count = 1;

        while (Post::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }

    /**
     * Сохраняет теги поста
     */
    private function syncTags(Post $post, array $newsData): void
    {
        if (!empty($newsData['tags'])) {
            $tagNames = array_map(
                fn($tag) => str_replace('#', '', $tag),
                $newsData['tags']
            );
            $post->syncTags($tagNames);
        }
    }
}
