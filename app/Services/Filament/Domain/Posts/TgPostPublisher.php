<?php

namespace App\Services\Filament\Domain\Posts;

use App\Enums\PostStatus;
use App\Jobs\CreateTgPost;
use App\Jobs\CreateTgPostJob;
use App\Jobs\UpdateTgPost;
use App\Models\Post;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TgPostPublisher
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
            if ($settings['telegram']) {
                $imagesFromPost = $post->images;
                $imagesFromContent = $this->extractImagesFromContent($post->content);
                $allImages = array_merge($imagesFromPost, $imagesFromContent);
                $imageLinks = $this->generateImagesForTg($allImages);

                $text = $this->generateContentForTg($post, $allImages);

                $videos = $this->extractVideosFromContent($post->content);
                $documents = $this->extractFilesFromContent($post->content);

                $publishDate = $post->publish_at > now() ? $post->publish_at->timestamp : null;
                dispatch(new CreateTgPostJob($post->id, $post->title, $text, $imageLinks, $videos, $documents, $publishDate));
            }
        }
    }

   public function update(array $settings, Post $post): void
   {
       Log::info(json_encode($settings));
       if ($post->status === PostStatus::PUBLISHED) {
           if ($settings['telegram']) {
               $text = $this->generateContentForTg($post->title, $post->content, $post->slug, $post->images);
               $images = $this->generateImagesForTg($post->images);
               $publishDate = $post->publish_at > now() ? $post->publish_at->timestamp : null;

               dispatch(new UpdateTgPost($text, $images, $post->id, $publishDate));
           }
       }
   }

    private function generateContentForTg($post, $images): string
    {
        $message = '';
        $message .= "*{$post->title}*\n\n";

        if (is_array($post->content)) {
            $message .= $this->parseContentBlocks($post->content);
        } elseif (is_string($post->content)) {
            $contentBlocks = json_decode($post->content, true);
            if (is_array($contentBlocks)) {
                $message .= $this->parseContentBlocks($contentBlocks);
            } else {
                $message .= $this->convertToMarkdown($post->content);
            }
        } else {
            $message .= $this->convertToMarkdown((string) $post->content);
        }

        if (isset($post->authors) && is_array($post->authors) && !empty($post->authors)) {
            $authors = array_map(function($author) {
                return json_decode('"' . $author . '"');
            }, $post->authors);
            $authorLabel = (count($authors) === 1) ? "Автор:" : "Авторы:";
            $message .= "\n\n$authorLabel " . implode(', ', $authors);
        }

        if ($post->slug) {
            $url = config('app.url') . "/posts/{$post->slug}";
            $linkText = "\n\n[Читать полностью]($url)";
            $linkLength = mb_strlen($linkText);
            $maxLength = $images ? 1024 : 4096;

            if (mb_strlen($message) > $maxLength - $linkLength) {
                $truncated = mb_substr($message, 0, $maxLength - $linkLength);
                $lastSpace = mb_strrpos($truncated, ' ');
                if ($lastSpace !== false) {
                    $truncated = mb_substr($truncated, 0, $lastSpace);
                }
                $message = $truncated . '...';
                $message .= $linkText;
            } elseif (count($images) > 10) {
                $message .= $linkText;
            }
        }

        return trim($message);
    }

    /**
     * Генерирует ссылки на изображения для Телеграмм.
     *
     * @param array $images
     * @return array
     */
    private function generateImagesForTg(array $images): array
    {
        return array_map(function ($file) {
            return Storage::url($file); // Генерируем полный URL для изображения
        }, $images);
    }

    private function parseContentBlocks(array $blocks): string
    {
        $text = '';
        foreach ($blocks as $block) {
            switch ($block['type'] ?? '') {
                case 'heading':
                    if (isset($block['data']['content'])) {
                        $level = $block['data']['level'] ?? 1; // Уровень заголовка, по умолчанию 1
                        $content = $this->convertToMarkdown($block['data']['content']);
                        $text .= str_repeat('#', $level) . ' ' . $content . "\n\n";
                    }
                    break;
                case 'paragraph':
                    if (isset($block['data']['content'])) {
                        $text .= $this->convertToMarkdown($block['data']['content']) . "\n\n";
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
        return $text;
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

    private function convertToMarkdown(string $text): string
    {
        // Декодируем HTML-сущности (например, &nbsp; становится \u00A0)
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');

        // Заменяем неразрывные пробелы на обычные
        $text = str_replace("\xc2\xa0", " ", $text); // \xc2\xa0 — это \u00A0 в UTF-8

        // Обрабатываем цитаты
        $text = preg_replace_callback('/<blockquote>(.*?)<\/blockquote>/s', function($match) {
            $content = $match[1];
            // Удаляем <p> и заменяем </p> на \n
            $content = preg_replace('/<p>(.*?)<\/p>/', "$1\n", $content);
            // Разбиваем на строки и убираем пустые
            $lines = array_filter(explode("\n", $content), function($line) {
                return trim($line) !== '';
            });
            // Добавляем > перед каждой непустой строкой
            $quoted = array_map(function($line) {
                return '> ' . trim($line);
            }, $lines);
            return implode("\n", $quoted) . "\n";
        }, $text);

        // Удаляем <p> и заменяем </p> на \n\n для параграфов
        $text = preg_replace('/<p>/', '', $text);
        $text = preg_replace('/<\/p>/', "\n", $text);

        // Обрабатываем жирный текст и курсив, убирая лишние пробелы
        $text = preg_replace_callback('/<strong>(.*?)<\/strong>/', function($match) {
            return '*' . trim($match[1]) . '*';
        }, $text);
        $text = preg_replace_callback('/<em>(.*?)<\/em>/', function($match) {
            return '_' . trim($match[1]) . '_';
        }, $text);
        // Удаляем оставшиеся HTML-теги
        $text = strip_tags($text);
        // Убираем лишние пробелы и пустые строки в начале и конце
        return trim($text);
    }
}