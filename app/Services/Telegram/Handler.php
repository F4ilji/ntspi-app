<?php

namespace App\Services\Telegram;

use App\Jobs\CreateVkPost;
use App\Services\VK\VkService;
use Carbon\Carbon;
use DefStudio\Telegraph\Handlers\WebhookHandler;
use DefStudio\Telegraph\Models\TelegraphBot;
use DefStudio\Telegraph\Models\TelegraphChat;
use App\Models\Post;
//use DefStudio\Telegraph\Keyboard\Button;
//use DefStudio\Telegraph\Keyboard\Keyboard;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\VK\VkAuthService;
use Illuminate\Support\Facades\Http;

class Handler extends WebhookHandler
{
    public function publish(): void
    {
        try {
            $post = Post::where('status', 'published')
                ->latest('publish_at')->first();

//            if (!$post) {
//                $this->reply("Нет опубликованных постов для отправки");
//                return;
//            }
            $chat = TelegraphChat::find(4);
            log::info($chat);
            $chat->markdown('zov')->send();

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

            if ($post->slug) {
                $url = config('app.url') . "posts/{$post->slug}";
                $message .= "\n\n[Читать полностью]($url)";
            }

            // Отправляем сообщение с Markdown
//            $this->chat->markdown($message)->send();

            // Обработка изображений и отправка сообщения в Telegram
            if ($post->images) {
                $images = is_string($post->images) ? json_decode($post->images, true) : $post->images;
                $baseUrl = config('app.url'); // Базовый URL, например, https://your-domain.com

                if (is_array($images) && !empty($images)) {
                    $mediaGroup = [];
                    foreach ($images as $index => $image) {
                        $mediaItem = [
                            'type' => 'photo',
                            'media' => config('app.url') . 'storage/' . ltrim($image, '/'),
                        ];
                        // Добавляем текст только к первому изображению
                        if ($index === 0) {
                            $mediaItem['caption'] = $message; // Основной текст поста
                            $mediaItem['parse_mode'] = 'Markdown'; // Поддержка Markdown
                        }
                        $mediaGroup[] = $mediaItem;
                    }

//                    $chat = TelegraphChat::find(0);
//                    $chat->mediaGroup($mediaGroup)->send();


//                    $page = $chat->page()->content($mediaGroup)->create();
//                    $bot = TelegraphBot::first();
//                    $page = $bot->page()->title($post->title)->content($post->content)->create();
//                    $articleUrl = $page->url();
//                    $url = "https://api.telegram.org/bot7746238162:AAFGi4XwIZ0lnnNL72tlxrmqAgbHmRBMFVs/sendMessage";
//
//                    $publishDateTime = strtotime('2025-04-27 20:27:00');
//                    $scheduleDate = Carbon::parse($publishDateTime)->timestamp;
//
//                    $data = [
//                        'chat_id' => $chat,
//                        'text' => $articleUrl,
//                        'schedule_date' => $scheduleDate
//                    ];
//
//                    $response = Http::post($url, $data);
//                    if ($response->failed()) {
//                        throw new \Exception('Ошибка при планировании поста: ' . $response->body());
//                    }


//                    $publishDate = $post->publish_at > now() ? $post->publish_at->timestamp : null;
//                    dispatch(new CreateVkPost($post->title, $message, $images, $post->id, $publishDate));

                } else {
                    $this->chat->markdown($message)->send();
                }
            } else {
                // Если нет изображений, отправляем только текст
                Log::info('dasdsa');
                $this->chat->markdown($message)->send();
            }
        } catch (\Exception $e) {
            Log::error('Ошибка при публикации в Telegram: ' . $e->getMessage());
            $this->reply("Произошла ошибка при отправке поста");
        }
    }

    private function parseContentBlocks(array $blocks): string
    {
        $text = '';
        foreach ($blocks as $block) {
            $content = $this->decodeUnicode($block['data']['content'] ?? '');
            switch ($block['type'] ?? '') {
                case 'heading':
                    $text .= "*" . $content . "*\n\n";
                    break;
                case 'paragraph':
                    $text .= $this->convertToMarkdown($content) . "\n\n";
                    break;
                case 'list':
                    if (isset($block['data']['items'])) {
                        foreach ($block['data']['items'] as $item) {
                            $text .= "- " . $item . "\n";
                        }
                        $text .= "\n";
                    }
                    break;
                default:
                    $text .= $content . "\n\n";
                    break;
            }
        }
        return trim($text);
    }

    private function convertToMarkdown(string $text): string
    {
        // Декодируем HTML-сущности
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        $text = $this->decodeUnicode($text);
        $text = preg_replace('/<p>(.*?)<\/p>/', '$1', $text);
        $text = preg_replace('/<b>(.*?)<\/b>/', '*$1*', $text);
        $text = preg_replace('/<i>(.*?)<\/i>/', '_$1_', $text);
        $text = preg_replace('/<strong>(.*?)<\/strong>/', '*$1*', $text);
        $text = preg_replace('/<em>(.*?)<\/em>/', '_$1_', $text);
        // Очищаем от HTML-тегов, декодируем оставшиеся сущности и экранируем спецсимволы для MarkdownV2
        return strip_tags($text);
    }

    private function decodeUnicode(string $text): string
    {
        return preg_replace_callback(
            '/\\\\u([0-9a-fA-F]{4})/',
            function ($match) {
                return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
            },
            $text
        );
    }

    protected function handleUnknownCommand(string|\Illuminate\Support\Stringable $command): void
    {
        $this->reply("Неизвестная команда: {$command}");
    }
}