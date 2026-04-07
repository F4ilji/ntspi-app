<?php

namespace App\Containers\Dashboard\Tasks\Posts;

use App\Containers\Article\Models\Post;
use App\Services\Filament\Domain\Posts\VkPostPublisher;
use Illuminate\Support\Facades\DB;

class PublishPostToVkTask
{
    /**
     * Публикует пост в VK
     *
     * @param Post $post
     * @param bool $shouldPublish Флаг публикации
     * @param bool $isUpdate Флаг обновления (true) или создания (false)
     * @return void
     */
    public function run(Post $post, bool $shouldPublish, bool $isUpdate = false): void
    {
        if (!$shouldPublish) {
            return;
        }

        $publisher = new VkPostPublisher();

        if ($isUpdate) {
            // Проверяем, опубликован ли пост уже в VK
            $postRelation = DB::table('posts_vk_posts')
                ->where('post_id', $post->id)
                ->first();

            if ($postRelation) {
                $publisher->update(['vk' => true], $post);
                return;
            }
        }

        $publisher->publish(['vk' => true], $post);
    }
}
