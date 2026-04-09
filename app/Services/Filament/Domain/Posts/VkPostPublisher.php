<?php

namespace App\Services\Filament\Domain\Posts;

use App\Containers\Article\Enums\PostStatus;
use App\Containers\Article\Models\Post;
use App\Jobs\CreateVkPost;
use App\Jobs\UpdateVkPost;
use Illuminate\Support\Facades\Storage;
use App\Jobs\CreateVkPostJob;
use App\Jobs\UpdateVkPostJob;
use Illuminate\Support\Facades\Log;

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
        if ($post->status === PostStatus::PUBLISHED) {
            if ($settings['vk']) {
                $text = $this->generateContentForVk($post);

                $imagesFromPost = $post->images ?? [];
                $imagesFromContent = $this->extractImagesFromContent($post->content);
                $allImages = array_merge($imagesFromPost, $imagesFromContent);
                $imageLinks = array_filter($this->generateImageLinksForVk($allImages)); // Удаляем null
                $videos = $this->extractVideosFromContent($post->content);

                $publishDate = $post->publish_at > now() ? $post->publish_at->timestamp : null;

                dispatch(new CreateVkPostJob(
                    $post->id,
                    $post->title,
                    $text,
                    $imageLinks,
                    $videos,
                    $publishDate));
            }
        }
    }

    public function update(array $settings, Post $post): void
    {
        if ($post->status === PostStatus::PUBLISHED) {
            if ($settings['vk']) {
                $text = $this->generateContentForVk($post);

                $imagesFromPost = $post->images ?? [];
                $imagesFromContent = $this->extractImagesFromContent($post->content);
                $allImages = array_merge($imagesFromPost, $imagesFromContent);
                $imageLinks = array_filter($this->generateImageLinksForVk($allImages)); // Удаляем null
                $videos = $this->extractVideosFromContent($post->content);

                $publishDate = $post->publish_at > now() ? $post->publish_at->timestamp : null;

                dispatch(new UpdateVkPostJob(
                    $post->id,
                    $post->title,
                    $text,
                    $imageLinks,
                    $videos,
                    $publishDate));
            }
        }
    }

    private function generateContentForVk($post): string
    {
        $message = '';
        $message .= "$post->title\n\n";

        if (is_array($post->content)) {
            $message .= $this->parseContentBlocks($post->content);
        } elseif (is_string($post->content)) {
            $contentBlocks = json_decode($post->content, true);
            if (is_array($contentBlocks)) {
                $message .= $this->parseContentBlocks($contentBlocks);
            } else {
                $message .= strip_tags($post->content);
            }
        }

        if (isset($post->authors) && is_array($post->authors) && !empty($post->authors)) {
            $authors = array_map(function($author) {
                return json_decode('"' . $author . '"');
            }, $post->authors);
            $authorLabel = (count($authors) === 1) ? "Автор:" : "Авторы:";
            $message .= "\n\n$authorLabel " . implode(', ', $authors);
        }

        // Добавляем ссылки на прикрепленные файлы
        $fileLinks = $this->generateFileLinks($post->content);
        if (!empty($fileLinks)) {
            $message .= "\n\n" . $fileLinks;
        }

        $message = html_entity_decode($message);

        return $message;
    }

    /**
     * Генерирует ссылки на файлы в формате VK [url|text]
     */
    private function generateFileLinks(array $content): string
    {
        $links = [];
        $baseUrl = config('app.url');

        foreach ($content as $block) {
            if ($block['type'] === 'files' && isset($block['data']['file']) && is_array($block['data']['file'])) {
                foreach ($block['data']['file'] as $file) {
                    if (isset($file['path']) && isset($file['title'])) {
                        $fileUrl = $baseUrl . '/storage/' . ltrim($file['path'], '/');
                        $fileName = $file['title'];
                        $links[] = "[{$fileUrl}|📎 {$fileName}]";
                    }
                }
            }
        }

        return !empty($links) 
            ? "Прикреплённые файлы:\n" . implode("\n", $links) 
            : '';
    }

    private function parseContentBlocks(array $blocks): string
    {
        $text = '';
        foreach ($blocks as $block) {
            switch ($block['type'] ?? '') {
                case 'heading':
                case 'paragraph':
                    if (isset($block['data']['content'])) {
                        $text .= strip_tags($block['data']['content']) . "\n\n";
                    }
                    break;
                case 'list':
                    if (isset($block['data']['items']) && is_array($block['data']['items'])) {
                        foreach ($block['data']['items'] as $item) {
                            $text .= "- " . $item . "\n";
                        }
                        $text .= "\n";
                    }
                    break;
            }
        }
        return trim($text);
    }

    private function extractImagesFromContent(array $blocks): array
    {
        $images = [];
        foreach ($blocks as $block) {
            if ($block['type'] === 'image' && isset($block['data']['url']) && is_array($block['data']['url'])) {
                foreach ($block['data']['url'] as $imagePath) {
                    $images[] = $imagePath;
                }
            }
        }
        return $images;
    }

    private function extractVideosFromContent(array $blocks): array
    {
        $videos = [];
        foreach ($blocks as $block) {
            if ($block['type'] === 'video' && isset($block['data']['path'])) {
                $path = Storage::url($block['data']['path']);
                $path = stripslashes($path);
                $videos[] = $path;
            }
        }
        return $videos;
    }

    private function generateImageLinksForVk(array $images): array
    {
        return array_map(function ($file) {
            // Пропускаем невалидные данные (пустые массивы, null и т.д.)
            if (!is_string($file) || empty($file)) {
                return null;
            }
            return Storage::url($file);
        }, $images);
    }
}

