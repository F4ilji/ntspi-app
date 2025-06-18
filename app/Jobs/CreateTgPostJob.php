<?php

namespace App\Jobs;


use App\Services\Telegram\TgService;
use App\Services\VK\Wall\VkWallService;
use Carbon\Carbon;
use DefStudio\Telegraph\Facades\Telegraph;
use DefStudio\Telegraph\Models\TelegraphChat;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CreateTgPostJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $post_id;
    protected $title;
    protected $message;
    protected $images;
    protected $videos;
    protected $documents;
    protected $publish_date;

    public function __construct(
        int $post_id, string $title, string $message, array $images = [], array $videos = [], array $documents = [], ?int $publish_date = null
    )
    {
        $this->post_id = $post_id;
        $this->title = $title;
        $this->message = $message;
        $this->images = $images;
        $this->videos = $videos;
        $this->documents = $documents;
        $this->publish_date = $publish_date;
    }

    public function handle()
    {
        $chat = TelegraphChat::find(4);
        $baseUrl = config('app.url');
        $messageIds = [];
        $captionSent = false;

        // Отправка медиагруппы, если есть изображения или видео
        if (!empty($this->images) || !empty($this->videos)) {
            $mediaGroup = $this->createMediaGroup($this->message, $this->images, $this->videos);
            $response = $chat->mediaGroup($mediaGroup)->send();

            if ($response->successful()) {
                $messages = $response->json()['result'];
                $messageIds = array_merge($messageIds, array_map(function($msg) {
                    return $msg['message_id'];
                }, $messages));
                Log::info('Media group sent: ' . json_encode($messageIds));
                $captionSent = true; // Подпись отправлена с медиагруппой
            } else {
                Log::error('Failed to send media group: ' . $response->body());
            }
        }

        // Отправка документов, если они есть
        if (!empty($this->documents)) {
            foreach ($this->documents as $index => $document) {
                $fullDocument = $baseUrl . $document;
                if ($index == 0 && !$captionSent) {
                    // Отправка первого документа с подписью, если она еще не отправлена
                    $response = $chat->document($fullDocument)->markdown($this->message)->send();
                    $captionSent = true;
                } else {
                    // Отправка документа без подписи
                    $response = $chat->document($fullDocument)->send();
                }

                if ($response->successful()) {
                    $messageId = $response->telegraphMessageId();
                    $messageIds[] = $messageId;
                    Log::info('Document sent: ' . $messageId);
                } else {
                    Log::error('Failed to send document: ' . $fullDocument . ' - ' . $response->body());
                }
            }
        }

        // Отправка текста, если ничего другого не отправлено
        if (!$captionSent) {
            $response = $chat->markdown($this->message)->send();

            if ($response->successful()) {
                $messageId = $response->telegraphMessageId();
                $messageIds[] = $messageId;
                Log::info('Text message sent: ' . $messageId);
            } else {
                Log::error('Failed to send text message: ' . $response->body());
            }
        }

        // Вставка записи в базу данных
        if (!empty($messageIds)) {
            $tgPostId = $messageIds[0];
            $mediaContent = count($messageIds) > 1 ? json_encode($messageIds) : null;
            $this->insertPostRecord($tgPostId, $mediaContent);
        }
    }

    private function insertPostRecord($tgPostId, $mediaIds)
    {
        $mediaContent = is_array($mediaIds) ? json_encode($mediaIds) : $mediaIds;

        DB::table('posts_tg_posts')->insert([
            'post_id' => $this->post_id,
            'tg_post_id' => $tgPostId,
            'media_content' => $mediaContent,
            'unchange_time_after' => Carbon::now()->addWeek(),
        ]);
    }

    private function createMediaGroup($text, $images, $videos)
    {
        try {
            $baseUrl = config('app.url');
            $mediaItems = [];

            // Добавление изображений
            foreach ($images as $image) {
                $mediaItems[] = [
                    'type' => 'photo',
                    'media' => $baseUrl . $image,
                ];
            }

            Log::info('Media items sent: ' . json_encode($mediaItems));

            // Добавление видео
            foreach ($videos as $video) {
                $mediaItems[] = [
                    'type' => 'video',
                    'media' => $baseUrl . $video,
                ];
            }

            // Ограничение до 10 элементов
            $mediaItems = array_slice($mediaItems, 0, 10);

            // Установка подписи для первого элемента
            if (!empty($mediaItems)) {
                $mediaItems[0]['caption'] = $text;
                $mediaItems[0]['parse_mode'] = 'Markdown';
            }

            return $mediaItems;
        } catch (\Exception $e) {
            Log::error('Ошибка при публикации в Telegram: ' . $e->getMessage());
            return [];
        }
    }
}

