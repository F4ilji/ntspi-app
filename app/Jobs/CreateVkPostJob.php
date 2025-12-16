<?php

namespace App\Jobs;

use App\Services\VK\Album\VkAlbumService;
use App\Services\VK\VkAuthService;
use App\Services\VK\VkService;
use App\Services\VK\Wall\VkWallService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use VK\Client\VKApiClient;

class CreateVkPostJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $post_id;
    protected $title;
    protected $message;
    protected $images;
    protected $videos;
    protected $documents;
    protected $publish_date;
    protected $public_id;

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
        $this->public_id = config('services.vk.public_id');
    }

    public function handle()
    {
        $logPrefix = "[VK Job {$this->post_id}]";
        Log::info("{$logPrefix} Начало обработки поста. Входящие изображения: " . count($this->images));

        $vk = new VKApiClient();
        $wallService = new VkWallService($vk);

        $from_group = 1;

        $doc_attachments = !empty($this->documents) ? $this->prepareDocuments($this->documents) : '';
        $doc_list = $doc_attachments ? explode(',', $doc_attachments) : [];
        $doc_count = count($doc_list);

        Log::info("{$logPrefix} Загружено документов: {$doc_count}");

        if ($doc_count >= 10) {
            $selected_attachments = array_slice($doc_list, 0, 10);
            $image_list = [];
            Log::info("{$logPrefix} Лимит вложений заполнен документами.");
        } else {
            $video_attachments = !empty($this->videos) ? $this->prepareVideos($this->videos) : '';
            $video_list = $video_attachments ? explode(',', $video_attachments) : [];
            $available_slots = 10 - $doc_count;
            $attached_videos = array_slice($video_list, 0, min(count($video_list), $available_slots));
            $remaining_slots = $available_slots - count($attached_videos);

            Log::info("{$logPrefix} Видео: " . count($attached_videos) . ", Осталось слотов для фото: {$remaining_slots}");

            if ($remaining_slots > 0 && !empty($this->images)) {
                $images_to_attach = array_slice($this->images, 0, $remaining_slots);

                Log::info("{$logPrefix} Начинаем подготовку фото для стены", ['files' => $images_to_attach]);

                $image_attachments = $this->prepareWallPhotos($images_to_attach);

                Log::info("{$logPrefix} Результат подготовки фото (строка): '{$image_attachments}'");

                $image_list = $image_attachments ? explode(',', $image_attachments) : [];
            } else {
                $image_list = [];
                Log::info("{$logPrefix} Фото не будут добавлены (нет слотов или нет фото).");
            }

            $selected_attachments = array_merge($doc_list, $attached_videos, $image_list);
        }

        if (count($this->images) > count($image_list)) {
            Log::info("{$logPrefix} Не все фото вошли в пост. Создаем альбом.");

            $album = $this->createAlbum($this->title, $this->images);
            if (isset($album['id'])) {
                $albumLink = "https://vk.ru/album-{$this->public_id}_{$album['id']}";
                $this->message .= "\n\n[{$albumLink}|Ссылка на все фотографии]";
            } else {
                Log::error("{$logPrefix} Не удалось создать альбом");
            }
        }

        $attachmentString = implode(',', $selected_attachments);
        Log::info("{$logPrefix} Финальная строка вложений: '{$attachmentString}'");

        $vk_post = $wallService->createPost($this->message, $from_group, $attachmentString, $this->publish_date);

        if (isset($vk_post['post_id'])) {
            Log::info("{$logPrefix} Пост успешно создан ID: {$vk_post['post_id']}");
            DB::table('posts_vk_posts')->insert(
                [
                    'post_id' => $this->post_id,
                    'vk_post_id' => $vk_post['post_id'],
                    'unchange_time_after' => Carbon::now()->addWeek(),
                ]
            );
        } else {
            Log::error("{$logPrefix} Ошибка создания поста в VK", ['response' => $vk_post]);
        }
    }

    private function prepareWallPhotos(array $images): string
    {
        try {
            $imagePaths = [];

            foreach ($images as $img) {
                $relativePath = ltrim($img, '/');

                $fullPath = public_path($relativePath);

                if (!file_exists($fullPath)) {
                    $fullPath = storage_path('app/public/' . $relativePath);
                }

                if (file_exists($fullPath)) {
                    $imagePaths[] = $fullPath;
                } else {
                    Log::warning("Файл не найден на диске: {$img} (Искали: {$fullPath})");
                }
            }

            if (empty($imagePaths)) {
                Log::info("Нет валидных локальных путей для фото.");
                return '';
            }

            Log::info("Локальные пути изображений для загрузки:", $imagePaths);

            $photos = $this->uploadWallPhotos($imagePaths, $this->public_id);

            $result = implode(',', array_map(function($photo) {
                return "photo{$photo['owner_id']}_{$photo['id']}";
            }, $photos));

            return $result;

        } catch (\Exception $e) {
            Log::error("Ошибка при подготовке фотографий: " . $e->getMessage());
            return '';
        }
    }

    private function uploadWallPhotos(array $imagePaths, ?int $groupId = null): array
    {
        $uploadedPhotos = [];

        foreach ($imagePaths as $imagePath) {
            try {
                Log::info("--- Загрузка фото: {$imagePath} ---");

                // 1. Проверка доступности файла
                $fileContent = @file_get_contents($imagePath);
                if ($fileContent === false) {
                    throw new \Exception("Не удалось прочитать файл по ссылке: {$imagePath}. Проверьте доступность URL.");
                }
                Log::info("Файл прочитан, размер: " . strlen($fileContent) . " байт.");

                // 2. Получение сервера
                $uploadServerResponse = Http::get('https://api.vk.ru/method/photos.getWallUploadServer', [
                    'group_id' => $groupId,
                    'access_token' => (new VkAuthService())->getToken()->access_token,
                    'v' => '5.131',
                ]);

                $uploadServerData = $uploadServerResponse->json();

                if (isset($uploadServerData['error'])) {
                    Log::error("VK API Error getWallUploadServer: ", $uploadServerData['error']);
                    continue;
                }

                if (!isset($uploadServerData['response']['upload_url'])) {
                    Log::error("Ответ getWallUploadServer не содержит upload_url", $uploadServerData);
                    throw new \Exception('Не удалось получить сервер для загрузки: ' . $imagePath);
                }
                $uploadUrl = $uploadServerData['response']['upload_url'];
                Log::info("Upload URL получен: {$uploadUrl}");

                // 3. Отправка файла
                $uploadResponse = Http::attach(
                    'photo',
                    $fileContent,
                    basename($imagePath)
                )->post($uploadUrl);

                $uploadData = $uploadResponse->json();

                // Логируем сырой ответ от сервера загрузки, часто ошибка тут
                Log::info("Результат POST загрузки:", $uploadData ?? []);

                if (!isset($uploadData['photo']) || !isset($uploadData['server']) || !isset($uploadData['hash'])) {
                    // Иногда VK возвращает пустой photo: "[]" или error внутри JSON
                    throw new \Exception('Некорректный ответ от сервера загрузки: ' . json_encode($uploadData));
                }

                // 4. Сохранение фото
                $saveResponse = Http::get('https://api.vk.ru/method/photos.saveWallPhoto', [
                    'group_id' => $groupId,
                    'photo' => $uploadData['photo'], // Это должна быть JSON строка, а не массив, если пришло строкой
                    'server' => $uploadData['server'],
                    'hash' => $uploadData['hash'],
                    'access_token' => (new VkAuthService())->getToken()->access_token,
                    'v' => '5.131',
                ]);

                $saveData = $saveResponse->json();

                if (isset($saveData['error'])) {
                    Log::error("VK API Error photos.saveWallPhoto: ", $saveData['error']);
                    continue;
                }

                if (!isset($saveData['response'][0])) {
                    Log::error("Не удалось сохранить фото (нет response[0])", $saveData);
                    throw new \Exception('Не удалось сохранить фотографию: ' . $imagePath);
                }

                $uploadedPhotos[] = $saveData['response'][0];
                Log::info("Фотография успешно сохранена. ID: " . $saveData['response'][0]['id']);

            } catch (\Exception $e) {
                Log::error("Критическая ошибка при загрузке {$imagePath}: " . $e->getMessage());
                Log::error($e->getTraceAsString());
            }
        }

        return $uploadedPhotos;
    }

    // ... (остальные методы prepareVideos, uploadVideos и т.д. остаются как были,
    // но можно добавить аналогичное логирование и туда при желании)

    private function prepareVideos(array $videos): string
    {
        $baseUrl = config('app.url');
        $videoPaths = array_map(function($vid) use ($baseUrl) {
            return $baseUrl . $vid;
        }, $videos);
        $uploadedVideos = $this->uploadVideos($videoPaths, $this->public_id);
        return implode(',', $uploadedVideos);
    }

    private function uploadVideos(array $videoPaths, ?int $groupId = null): array
    {
        $uploadedVideos = [];
        // ... (Ваш старый код uploadVideos)
        foreach ($videoPaths as $videoPath) {
            // Для краткости оставил как есть, но лучше тоже обернуть в try-catch с логами
            try {
                $saveResponse = Http::timeout(360)->get('https://api.vk.ru/method/video.save', [
                    'group_id' => $groupId,
                    'access_token' => (new VkAuthService())->getToken()->access_token,
                    'v' => '5.131',
                ]);

                $saveData = $saveResponse->json();
                if (!isset($saveData['response']['upload_url'])) {
                    throw new \Exception('Не удалось получить сервер для загрузки видео: ' . $videoPath);
                }
                $uploadUrl = $saveData['response']['upload_url'];

                $uploadResponse = Http::timeout(360)->attach(
                    'video_file',
                    file_get_contents($videoPath),
                    basename($videoPath)
                )->post($uploadUrl);

                $uploadData = $uploadResponse->json();
                if (!isset($uploadData['video_id']) || !isset($uploadData['owner_id'])) {
                    throw new \Exception('Не удалось загрузить видео: ' . $videoPath);
                }

                $attachment = "video{$uploadData['owner_id']}_{$uploadData['video_id']}";
                $uploadedVideos[] = $attachment;
            } catch (\Exception $e) {
                Log::error("Ошибка при загрузке видео {$videoPath}: " . $e->getMessage());
            }
        }
        return $uploadedVideos;
    }

    private function prepareDocuments(array $documents): string
    {
        // Ваш старый код, добавил только лог ошибки
        $baseUrl = config('app.url');
        try {
            $documentPaths = array_map(function($doc) use ($baseUrl) {
                return $baseUrl . $doc;
            }, $documents);
            // Log::info(json_encode($documentPaths)); // Старый лог
            $uploadedDocs = $this->uploadDocuments($documentPaths, $this->public_id);
            return implode(',', $uploadedDocs);
        } catch (\Exception $e) {
            Log::error("Ошибка при подготовке документов: " . $e->getMessage());
            return '';
        }
    }

    private function uploadDocuments(array $documentPaths, ?int $groupId = null): array
    {
        // Ваш старый код
        $uploadedDocs = [];

        foreach ($documentPaths as $docPath) {
            try {
                $uploadServerResponse = Http::get('https://api.vk.ru/method/docs.getWallUploadServer', [
                    'group_id' => $groupId,
                    'access_token' => (new VkAuthService())->getToken()->access_token,
                    'v' => '5.131',
                ]);

                $uploadServerData = $uploadServerResponse->json();
                // Log::info($uploadServerData); // Старый лог
                if (!isset($uploadServerData['response']['upload_url'])) {
                    throw new \Exception('Не удалось получить сервер для загрузки документа: ' . $docPath);
                }
                $uploadUrl = $uploadServerData['response']['upload_url'];

                $uploadResponse = Http::attach(
                    'file',
                    file_get_contents($docPath),
                    basename($docPath)
                )->post($uploadUrl);

                $uploadData = $uploadResponse->json();
                if (!isset($uploadData['file'])) {
                    throw new \Exception('Не удалось загрузить документ: ' . $docPath);
                }

                $saveResponse = Http::get('https://api.vk.ru/method/docs.save', [
                    'file' => $uploadData['file'],
                    'access_token' => (new VkAuthService())->getToken()->access_token,
                    'v' => '5.131',
                ]);

                $saveData = $saveResponse->json();

                if (!isset($saveData['response']['doc'])) {
                    throw new \Exception('Не удалось сохранить документ: ' . $docPath);
                }

                $doc = $saveData['response']['doc'];
                $attachment = "doc{$doc['owner_id']}_{$doc['id']}";
                $uploadedDocs[] = $attachment;
            } catch (\Exception $e) {
                Log::error("Ошибка при загрузке документа {$docPath}: " . $e->getMessage());
            }
        }
        return $uploadedDocs;
    }

    public function createAlbum(string $title, $images)
    {
        // Ваш код
        $vk = new VKApiClient();

        $album = (new VkAlbumService($vk))->createAlbum($title);
        // Добавим проверку
        if(!isset($album['id'])) return [];

        $uploadServer = (new VkAlbumService($vk))->getServerForUploadImages($album['id'], config('PUBLIC_ID'));
        foreach (array_chunk($images, 4) as $images_slice) {
            $images_data = (new VkAlbumService($vk))->uploadImagesToUploadServer($uploadServer['upload_url'], $images_slice);
            (new VkAlbumService($vk))->saveImagesToUploadServer(
                $images_data['aid'],
                $images_data['server'],
                $images_data['photos_list'],
                $images_data['hash']
            );
        }
        return $album;
    }
}