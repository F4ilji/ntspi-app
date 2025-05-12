<?php

namespace App\Services\Filament\Domain\Posts;

use App\Containers\Article\Enums\PostStatus;
use App\Containers\Article\Models\Post;
use App\Jobs\CreateVkPost;
use App\Jobs\UpdateVkPost;
use Illuminate\Support\Facades\Storage;

class VkPostPublisher
{
    /**
     * Публикует пост в социальных сетях.
     *
     * @param array $settings
     * @param Post $post
     * @return void
     */
    public function publish(array $settings, Post $post): void
    {
        if ($post->status->value === PostStatus::PUBLISHED->value) {
            if ($settings['vk']) {
                $text = $this->generateContentForVk($post->content);
                $images = $this->generateImageLinksForVk($post->images);
                $publishDate = $post->publish_at > now() ? $post->publish_at->timestamp : null;

                dispatch(new CreateVkPost($post->title, $text, $images, $post->id, $publishDate));
            }
        }
    }

    public function update(array $settings, Post $post): void
    {
        if ($post->status->value === PostStatus::PUBLISHED->value) {
            if ($settings['vk']) {

                $text = $this->generateContentForVk($post->content);
                $images = $this->generateImageLinksForVk($post->images);
                $publishDate = $post->publish_at > now() ? $post->publish_at->timestamp : null;

                dispatch(new UpdateVkPost($post->title, $text, $images, $post->id, $publishDate));
            }
        }
    }

    /**
     * Генерирует текстовый контент для ВКонтакте.
     *
     * @param array $content
     * @return string
     */
    private function generateContentForVk(array $content): string
    {
        $text = '';
        foreach ($content as $block) {
            switch ($block['type']) {
                case 'paragraph':
                    $text .= strip_tags($block['data']['content']) . "\n\n";
                    break;
                case 'heading':
                    $text .= strip_tags($block['data']['content']) . "\n\n";
                    break;
            }
        }
        return trim($text);
    }

    /**
     * Генерирует ссылки на изображения для ВКонтакте.
     *
     * @param array $images
     * @return array
     */
    private function generateImageLinksForVk(array $images): array
    {
        return array_map(function ($file) {
            return Storage::url($file); // Генерируем полный URL для изображения
        }, $images);
    }
}
