<?php

namespace App\Containers\Dashboard\Tasks\Posts;

use App\Containers\Article\Models\Post;
use App\Services\Filament\Domain\Posts\PostDataProcessor;
use Illuminate\Support\Str;

class CreatePostTask
{
    public function __construct(
        private readonly PostDataProcessor $postDataProcessor,
    ) {}

    /**
     * Создает новый пост в БД
     *
     * @param array $data Данные поста
     * @return Post
     */
    public function run(array $data): Post
    {
        $processedData = $this->postDataProcessor->processCreate($data);

        $tags = $processedData['tags'] ?? null;
        
        // Удаляем теги из данных, так как они обрабатываются отдельно
        unset($processedData['tags']);
        
        $post = Post::create($processedData);
        
        // Синхронизируем теги если они есть
        if (!empty($tags) && is_array($tags)) {
            $post->syncTags($tags);
        }
        
        return $post;
    }

    /**
     * Генерирует уникальный slug
     */
    public function generateUniqueSlug(string $baseSlug): string
    {
        $slug = $baseSlug;
        $count = 1;

        while (Post::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }
}
