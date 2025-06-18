<?php

namespace App\Services\Telegram;

use DefStudio\Telegraph\Models\TelegraphChat;
use Illuminate\Support\Facades\Log;

class TgService
{
//    public function createPost($text, $images, $videos, $documents, $publishDate)
//    {
//        $chat = TelegraphChat::find(4);
//        Log::info($images);
//        if ($images !== []){
//            $images = array_slice($images, 0, 10);
//            $mediaGroup = $this->createMediaGroup($text, $images);
//            $response = $chat->mediaGroup($mediaGroup)->send();
//            if ($response->successful()) {
//                $messages = $response->json()['result'];
//                $mediaGroupId = $messages[0]['message_id'];
//                $messageIds = array_map(function($msg) {
//                    return $msg['message_id'];
//                }, $messages);
//                return [$mediaGroupId, $messageIds];
//            }
//        }
//        else{
//            $response = $chat->markdown($text)->send();
//            if ($response->successful()) {
//                $messageId = $response->telegraphMessageId();
//            }
//            return [$messageId, null];
//        }
//    }

    public function createPost($text, $images, $videos, $documents, $publishDate)
    {
        $chat = TelegraphChat::find(4);
        Log::info($images);
        Log::info($videos);
        Log::info($documents);

        if (!empty($images) || !empty($videos)) {
            // Создаем медиагруппу из изображений и видео (до 10 элементов)
            $mediaItems = [];
           foreach ($images as $image) {
               $mediaItems[] = ['type' => 'photo', 'media' => $image];
           }
            foreach ($videos as $video) {
                $mediaItems[] = ['type' => 'video', 'media' => $video];
            }
            $mediaItems = array_slice($mediaItems, 0, 10);
            if (!empty($mediaItems)) {
                $mediaItems[0]['caption'] = $text;
                $mediaItems[0]['parse_mode'] = 'Markdown';
            }
            $response = $chat->mediaGroup($mediaItems)->send();
            if ($response->successful()) {
                $messages = $response->json()['result'];
                $mainMessageId = $messages[0]['message_id'];
                $mediaMessageIds = array_map(function($msg) {
                    return $msg['message_id'];
                }, $messages);
            }

//            // Отправляем документы
//            $documentMessageIds = [];
//            foreach ($documents as $document) {
//                $response = $chat->document($document)->caption($text)->parseMode('Markdown')->send();
//                if ($response->successful()) {
//                    $documentMessageIds[] = $response->telegraphMessageId();
//                }
//            }
//            $allMessageIds = array_merge($mediaMessageIds, $documentMessageIds);
            return [$mainMessageId, $mediaMessageIds];
        } elseif (!empty($documents)) {
            // Отправляем только документы
            $documentMessageIds = [];
            $mainMessageId = null;
            foreach ($documents as $index => $document) {
                $response = $chat->document($document)->caption($text)->parseMode('Markdown')->send();
                if ($response->successful()) {
                    $messageId = $response->telegraphMessageId();
                    $documentMessageIds[] = $messageId;
                    if ($index == 0) {
                        $mainMessageId = $messageId;
                    }
                }
            }
            return [$mainMessageId, $documentMessageIds];
        } else {
            // Отправляем только текст
            $response = $chat->markdown($text)->send();
            if ($response->successful()) {
                $messageId = $response->telegraphMessageId();
                return [$messageId, [$messageId]];
            }
        }
    }

    public function updatePost($post_id, $text, $images, $media_ids)
    {
        $chat = TelegraphChat::find(1);

        if ($images !== []) {
            try {
//                if (count($images) !== count($media_ids)) {
//                    foreach ($media_ids as $messageId) {
//                        $chat->deleteMessage($messageId)->send();
//                    }
//
//                    $modifiedText = $text . "\n\n_(сообщение изменено)_";
//
//                    $result = $this->createPost($modifiedText, $images);
//
//                    return [
//                        'post_id' => $result[0],
//                        'media_ids' => $result[1] ?? []
//                    ];
//                }

                $firstMessageId = $media_ids[0];
                $chat->editCaption($firstMessageId)
                    ->markdown($text)
                    ->send();

//                $messageIds = [];
//                $firstMessageId = null;

//                foreach ($media_ids as $index => $messageId) {
//                    if (!isset($images[$index])) {
//                        continue;
//                    }
//
//                    $newMedia = $images[$index];
//                    $response = $chat->editMedia($messageId)
//                        ->photo(config('app.url') . $newMedia)
//                        ->send();
//
//                    if ($response->successful()) {
//                        $updatedMessageId = $response->json()['result']['message_id'];
//                        $messageIds[] = $updatedMessageId;
//
//                        if ($index === 0) {
//                        }
//                    }
//                }

            } catch (\Exception $e) {
                Log::error('Ошибка при изменении поста ' . $e->getMessage());
                return []; // Или null/исключение
            }
        } else {
            // Для текстовых сообщений
            $chat->edit($post_id)
                ->markdown($text)
                ->send();
        }



//        $chat = TelegraphChat::find(4);
//        if ($images !== []){
//            try {
//                Log::info(json_encode($media_ids));
//                $firstMessageId = $media_ids[0];
//                foreach ($media_ids as $index => $messageId) {
//                    $newMedia = $images[$index];
//                    Log::info($newMedia);
//                    Log::info($messageId);
//                    $chat->editMedia($messageId)
//                        ->photo(config('app.url') . $newMedia)
//                        ->send();
//                }
//                $chat->editCaption($firstMessageId)
//                    ->markdown($text)
//                    ->send();
//
//            } catch (\Exception $e) {
//                Log::error('Ошибка при изменении поста ' . $e->getMessage());
//            }
//        }
//        else{
//            $chat->edit($post_id)->markdown($text)->send();
//        }
    }

    private function createMediaGroup($text, $images)
    {
        try {
            $baseUrl = config('app.url');

            if (is_array($images) && !empty($images)) {
                $mediaGroup = [];
                foreach ($images as $index => $image) {
                    $mediaItem = [
                        'type' => 'photo',
                        'media' => $baseUrl . $image,
//                        https://crack-gar-specially.ngrok-free.app/storage/posts/gallery/01JT5R37Z05S119VCRRFEADSAG.jpg
                    ];

                    if ($index === 0) {
                        $mediaItem['caption'] = $text;
                        $mediaItem['parse_mode'] = 'Markdown';
                    }
                    $mediaGroup[] = $mediaItem;
                }
                return $mediaGroup;
            }
        } catch (\Exception $e) {
            Log::error('Ошибка при публикации в Telegram: ' . $e->getMessage());
        }
    }
}