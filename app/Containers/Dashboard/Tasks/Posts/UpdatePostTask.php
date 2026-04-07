<?php

namespace App\Containers\Dashboard\Tasks\Posts;

use App\Containers\Article\Models\Post;
use App\Services\Filament\Domain\Posts\PostDataProcessor;

class UpdatePostTask
{
    public function __construct(
        private readonly PostDataProcessor $postDataProcessor,
    ) {}

    /**
     * Обновляет существующий пост
     *
     * @param Post $post Пост для обновления
     * @param array $data Новые данные
     * @return Post
     */
    public function run(Post $post, array $data): Post
    {
        $processedData = $this->postDataProcessor->processUpdate([
            ...$post->toArray(),
            ...$data,
        ]);

        $post->update($processedData);

        return $post->fresh();
    }
}
