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

                $imagesFromPost = $post->images;
                $imagesFromContent = $this->extractImagesFromContent($post->content);
                $allImages = array_merge($imagesFromPost, $imagesFromContent);
                $imageLinks = $this->generateImageLinksForVk($allImages);
                $videos = $this->extractVideosFromContent($post->content);
                $documents = $this->extractFilesFromContent($post->content);

                $publishDate = $post->publish_at > now() ? $post->publish_at->timestamp : null;

                dispatch(new CreateVkPostJob(
                    $post->id,
                    $post->title,
                    $text,
                    $imageLinks,
                    $videos,
                    $documents,
                    $publishDate));
            }
        }
    }

    public function update(array $settings, Post $post): void
    {
        if ($post->status === PostStatus::PUBLISHED) {
            if ($settings['vk']) {
                $text = $this->generateContentForVk($post);

                $imagesFromPost = $post->images;
                $imagesFromContent = $this->extractImagesFromContent($post->content);
                $allImages = array_merge($imagesFromPost, $imagesFromContent);
                $imageLinks = $this->generateImageLinksForVk($allImages);
                $videos = $this->extractVideosFromContent($post->content);
                $documents = $this->extractFilesFromContent($post->content);

                $publishDate = $post->publish_at > now() ? $post->publish_at->timestamp : null;

                dispatch(new UpdateVkPostJob(
                    $post->id,
                    $post->title,
                    $text,
                    $imageLinks,
                    $videos,
                    $documents,
                    $publishDate));
            }
        }

//
//        if ($post->status === PostStatus::PUBLISHED) {
//            if ($settings['vk']) {
//                $text = $this->generateContentForVk($post->title, $post->content);
//                $images = $this->generateImageLinksForVk($post->images);
//                $publishDate = $post->publish_at > now() ? $post->publish_at->timestamp : null;
//
//                dispatch(new UpdateVkPost($post->title, $text, $images, $post->id, $publishDate));
//            }
//        }
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

        $message = html_entity_decode($message);

//        $maxLength = 16000;
//        $reservedSpace = 200;
//        $linkText = '';

//        if ($post->slug) {
//            $url = config('app.url') . "/posts/{$post->slug}";
//            $linkText = "\n\n[{$url}|Читать полностью]";
//        }
//
//        $linkLength = mb_strlen($linkText);
//
//        $maxMLength = $maxLength - $reservedSpace - $linkLength;
//
//        if (mb_strlen($message) > $maxMLength) {
//            $truncated = mb_substr($message, 0, $maxMLength);
//            $lastSpace = mb_strrpos($truncated, ' ');
//            if ($lastSpace !== false) {
//                $truncated = mb_substr($truncated, 0, $lastSpace);
//            }
//            $message = $truncated . '...';
//        }
//
//        $message .= $linkText;

        return $message;
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

    private function extractFilesFromContent(array $blocks): array
    {
        $files = [];
        foreach ($blocks as $block) {
            if ($block['type'] === 'files' && isset($block['data']['file']) && is_array($block['data']['file'])) {
                foreach ($block['data']['file'] as $file) {
                    if (isset($file['path'])) {
                        $files[] = Storage::url($file['path']);
                    }
                }
            }
        }
        return $files;
    }

    private function generateImageLinksForVk(array $images): array
    {
        return array_map(function ($file) {
            return Storage::url($file); // Генерируем полный URL для изображения
        }, $images);
    }
}

