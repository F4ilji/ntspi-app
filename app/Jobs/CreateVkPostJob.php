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
    protected $publish_date;
    protected $public_id;
    protected $primary_attachments_mode;

    public function __construct(
        int $post_id, string $title, string $message, array $images = [], array $videos = [], ?int $publish_date = null, string $primary_attachments_mode = 'grid'
    )
    {
        $this->post_id = $post_id;
        $this->title = $title;
        $this->message = $message;
        $this->images = $images;
        $this->videos = $videos;
        $this->publish_date = $publish_date;
        $this->public_id = config('services.vk.public_id');
        $this->primary_attachments_mode = $primary_attachments_mode;
    }

    public function handle()
    {
        Log::channel('vk')->info('Processing VK post', ['post_id' => $this->post_id, 'images' => count($this->images)]);

        $vk = new VKApiClient();
        $wallService = new VkWallService($vk);

        $from_group = 1;

        // Документы больше не загружаются — они добавляются как ссылки в тексте поста
        // Считаем только видео и изображения
        $video_attachments = !empty($this->videos) ? $this->prepareVideos($this->videos) : '';
        $video_list = $video_attachments ? explode(',', $video_attachments) : [];
        
        // Все 10 слотов доступны для видео и изображений
        $available_slots = 10 - count($video_list);
        $attached_videos = array_slice($video_list, 0, count($video_list));
        $remaining_slots = $available_slots;

        Log::channel('vk')->debug('Attachment slots calculated', [
            'post_id' => $this->post_id,
            'videos' => count($attached_videos),
            'remaining_photo_slots' => $remaining_slots,
        ]);

        $image_list = [];
        if ($remaining_slots > 0 && !empty($this->images)) {
            $images_to_attach = array_slice($this->images, 0, $remaining_slots);

            Log::channel('vk')->debug('Preparing wall photos', ['files' => $images_to_attach]);

            $image_attachments = $this->prepareWallPhotos($images_to_attach);

            Log::channel('vk')->debug('Photo preparation result', ['attachments' => $image_attachments]);

            $image_list = $image_attachments ? explode(',', $image_attachments) : [];
        } else {
            Log::channel('vk')->debug('No photos to attach — no slots or no images', ['post_id' => $this->post_id]);
        }

        $selected_attachments = array_merge($attached_videos, $image_list);

        if (count($this->images) > count($image_list)) {
            Log::channel('vk')->debug('Not all photos fit in post — creating album', ['post_id' => $this->post_id]);

            $album = $this->createAlbum($this->title, $this->images);
            if (isset($album['id'])) {
                $albumLink = "https://vk.ru/album-{$this->public_id}_{$album['id']}";
                $this->message .= "\n\n[{$albumLink}|Ссылка на все фотографии]";
            } else {
                Log::channel('vk')->error('Failed to create VK album', ['post_id' => $this->post_id]);
            }
        }

        $attachmentString = implode(',', $selected_attachments);
        Log::channel('vk')->debug('Final attachment string', ['post_id' => $this->post_id, 'attachments' => $attachmentString]);

        $vk_post = $wallService->createPost($this->message, $from_group, $attachmentString, $this->publish_date, $this->primary_attachments_mode);

        if (isset($vk_post['post_id'])) {
            Log::channel('vk')->info('VK post created successfully', ['post_id' => $this->post_id, 'vk_post_id' => $vk_post['post_id']]);
            DB::table('posts_vk_posts')->insert(
                [
                    'post_id' => $this->post_id,
                    'vk_post_id' => $vk_post['post_id'],
                    'unchange_time_after' => Carbon::now()->addWeek(),
                ]
            );
        } else {
            Log::channel('vk')->error('Failed to create VK post', ['post_id' => $this->post_id, 'response' => $vk_post]);
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
                    Log::channel('vk')->warning('Image file not found on disk', ['image' => $img, 'expected_path' => $fullPath]);
                }
            }

            if (empty($imagePaths)) {
                Log::channel('vk')->debug('No valid local image paths found', ['post_id' => $this->post_id]);
                return '';
            }

            Log::channel('vk')->debug('Local image paths for upload', ['paths' => $imagePaths]);

            $photos = $this->uploadWallPhotos($imagePaths, $this->public_id);

            $result = implode(',', array_map(function($photo) {
                return "photo{$photo['owner_id']}_{$photo['id']}";
            }, $photos));

            return $result;

        } catch (\Exception $e) {
            Log::channel('vk')->error('Failed to prepare wall photos', ['error' => $e->getMessage()]);
            return '';
        }
    }

    private function uploadWallPhotos(array $imagePaths, ?int $groupId = null): array
    {
        $uploadedPhotos = [];

        foreach ($imagePaths as $imagePath) {
            try {
                Log::channel('vk')->debug('Uploading photo', ['path' => $imagePath]);

                // 1. Проверка доступности файла
                $fileContent = @file_get_contents($imagePath);
                if ($fileContent === false) {
                    throw new \Exception("Не удалось прочитать файл по ссылке: {$imagePath}. Проверьте доступность URL.");
                }
                Log::channel('vk')->debug('File read successfully', ['path' => $imagePath, 'size_bytes' => strlen($fileContent)]);

                // 2. Получение сервера
                $uploadServerResponse = Http::get('https://api.vk.ru/method/photos.getWallUploadServer', [
                    'group_id' => $groupId,
                    'access_token' => (new VkAuthService())->getToken()->access_token,
                    'v' => '5.131',
                ]);

                $uploadServerData = $uploadServerResponse->json();

                if (isset($uploadServerData['error'])) {
                    Log::channel('vk')->error('VK API getWallUploadServer error', ['error' => $uploadServerData['error']]);
                    continue;
                }

                if (!isset($uploadServerData['response']['upload_url'])) {
                    Log::channel('vk')->error('getWallUploadServer response missing upload_url', ['response' => $uploadServerData]);
                    throw new \Exception('Не удалось получить сервер для загрузки: ' . $imagePath);
                }
                $uploadUrl = $uploadServerData['response']['upload_url'];
                Log::channel('vk')->debug('Upload URL obtained', ['url' => $uploadUrl]);

                // 3. Отправка файла
                $uploadResponse = Http::attach(
                    'photo',
                    $fileContent,
                    basename($imagePath)
                )->post($uploadUrl);

                $uploadData = $uploadResponse->json();

                // Логируем сырой ответ от сервера загрузки, часто ошибка тут
                Log::channel('vk')->debug('Upload POST response', ['response' => $uploadData ?? []]);

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
                    Log::channel('vk')->error('VK API saveWallPhoto error', ['error' => $saveData['error']]);
                    continue;
                }

                if (!isset($saveData['response'][0])) {
                    Log::channel('vk')->error('saveWallPhoto missing response[0]', ['response' => $saveData]);
                    throw new \Exception('Не удалось сохранить фотографию: ' . $imagePath);
                }

                $uploadedPhotos[] = $saveData['response'][0];
                Log::channel('vk')->debug('Photo saved successfully', ['photo_id' => $saveData['response'][0]['id']]);

            } catch (\Exception $e) {
                Log::channel('vk')->error('Critical error uploading photo', ['path' => $imagePath, 'error' => $e->getMessage()]);
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
                Log::channel('vk')->error('Failed to upload video', ['path' => $videoPath, 'error' => $e->getMessage()]);
            }
        }
        return $uploadedVideos;
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