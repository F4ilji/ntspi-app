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

class UpdateVkPostJob implements ShouldQueue
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
        $this->public_id = env('PUBLIC_ID');
    }

    public function handle()
    {
        $vk = new VKApiClient();
        $wallService = new VkWallService($vk);
        $albumService = new VkAlbumService($vk);

        $postRelation = DB::table('posts_vk_posts')->select()->where('post_id', $this->post_id)->first();
        $this->post_id = $postRelation->vk_post_id;

        $vk_post = $wallService->getPostById($this->post_id);

        // доделать
        if ($vk_post['attachments']) {
            Log::info($vk_post);
            if ($this->getAlbumAttachment($vk_post['attachments'])) {
                Log::info('вход в аттачментс вк');
                $album = $this->getAlbumAttachment($vk_post['attachments']);
                Log::info($album);
                $albumService->deleteAlbum($album['album']['id'], $this->public_id);
            }
        }

        $from_group = 1;

        // Подготовка документов
        $doc_attachments = !empty($this->documents) ? $this->prepareDocuments($this->documents) : '';
        $doc_list = $doc_attachments ? explode(',', $doc_attachments) : [];
        $doc_count = count($doc_list);

        if ($doc_count >= 10) {
            // Если документов 10 или больше, прикрепляем только первые 10
            $selected_attachments = array_slice($doc_list, 0, 10);
            $image_list = [];
        } else {
            // Подготовка видео
            $video_attachments = !empty($this->videos) ? $this->prepareVideos($this->videos) : '';
            $video_list = $video_attachments ? explode(',', $video_attachments) : [];
            $available_slots = 10 - $doc_count;
            $attached_videos = array_slice($video_list, 0, min(count($video_list), $available_slots));
            $remaining_slots = $available_slots - count($attached_videos);

            if ($remaining_slots > 0 && !empty($this->images)) {
                // Подготовка только тех изображений, которые поместятся
                $images_to_attach = array_slice($this->images, 0, $remaining_slots);
                $image_attachments = $this->prepareWallPhotos($images_to_attach);
                $image_list = $image_attachments ? explode(',', $image_attachments) : [];
            } else {
                $image_list = [];
            }

            // Собираем все вложения с учетом приоритетов
            $selected_attachments = array_merge($doc_list, $attached_videos, $image_list);
        }

        // Проверяем, все ли изображения прикреплены
        if (count($this->images) > count($image_list)) {
            // Если не все изображения вошли, создаем альбом
            $album = $this->createAlbum($this->title, $this->images);
            if (isset($album['id'])) {
                $albumLink = "https://vk.com/album-{$this->public_id}_{$album['id']}";
                $this->message .= "\n\n[{$albumLink}|Ссылка на все фотографии]";
            } else {
                Log::error('Не удалось создать альбом');
            }
        }

        $attachmentString = implode(',', $selected_attachments);

        $wallService->updatePost($this->post_id, $this->message, $from_group, $attachmentString, $this->publish_date);
    }

    private function getAlbumAttachment(array $attachments)
    {
        $data = collect($attachments);
        $filteredAttachment = $data->filter(function($attachment) {
            return $attachment['type'] === 'album';
        });
        return (isset($filteredAttachment[0]) ? $filteredAttachment[0] : null);
    }

    private function prepareWallPhotos(array $images): string
    {
        $baseUrl = config('app.url');
        try {
            $imagePaths = array_map(function($img) use ($baseUrl) {
                return $baseUrl . $img;
            }, $images);

            $photos = $this->uploadWallPhotos($imagePaths, $this->public_id);

            return implode(',', array_map(function($photo) {
                return "photo{$photo['owner_id']}_{$photo['id']}";
            }, $photos));
        } catch (\Exception $e) {
            Log::error("Ошибка при подготовке фотографий: " . $e->getMessage());
            return '';
        }
    }

    private function prepareVideos(array $videos): string
    {
        $baseUrl = config('app.url');
        $videoPaths = array_map(function($vid) use ($baseUrl) {
            return $baseUrl . $vid;
        }, $videos);
        $uploadedVideos = $this->uploadVideos($videoPaths, $this->public_id);
        return implode(',', $uploadedVideos);
    }

    private function prepareDocuments(array $documents): string
    {
        $baseUrl = config('app.url');
        try {
            $documentPaths = array_map(function($doc) use ($baseUrl) {
                return $baseUrl . $doc;
            }, $documents);
            Log::info(json_encode($documentPaths));
            $uploadedDocs = $this->uploadDocuments($documentPaths, $this->public_id);
            return implode(',', $uploadedDocs);
        } catch (\Exception $e) {
            Log::error("Ошибка при подготовке документов: " . $e->getMessage());
            return '';
        }
    }

    private function uploadWallPhotos(array $imagePaths, ?int $groupId = null): array
    {
        $uploadedPhotos = [];

        foreach ($imagePaths as $imagePath) {
            try {
                $uploadServerResponse = Http::get('https://api.vk.com/method/photos.getWallUploadServer', [
                    'group_id' => $groupId,
                    'access_token' => (new VkAuthService())->getToken()->access_token,
                    'v' => '5.131',
                ]);

                $uploadServerData = $uploadServerResponse->json();
                if (!isset($uploadServerData['response']['upload_url'])) {
                    throw new \Exception('Не удалось получить сервер для загрузки: ' . $imagePath);
                }
                $uploadUrl = $uploadServerData['response']['upload_url'];

                $uploadResponse = Http::attach(
                    'photo',
                    file_get_contents($imagePath),
                    basename($imagePath)
                )->post($uploadUrl);

                $uploadData = $uploadResponse->json();
                if (!isset($uploadData['photo']) || !isset($uploadData['server']) || !isset($uploadData['hash'])) {
                    throw new \Exception('Не удалось загрузить фотографию: ' . $imagePath);
                }

                $saveResponse = Http::get('https://api.vk.com/method/photos.saveWallPhoto', [
                    'group_id' => $groupId,
                    'photo' => $uploadData['photo'],
                    'server' => $uploadData['server'],
                    'hash' => $uploadData['hash'],
                    'access_token' => (new VkAuthService())->getToken()->access_token,
                    'v' => '5.131',
                ]);

                $saveData = $saveResponse->json();
                if (!isset($saveData['response'][0])) {
                    throw new \Exception('Не удалось сохранить фотографию: ' . $imagePath);
                }

                $uploadedPhotos[] = $saveData['response'][0];
                Log::info("Фотография загружена. ID: " . $saveData['response'][0]['id']);
            } catch (\Exception $e) {
                Log::error("Ошибка при загрузке {$imagePath}: " . $e->getMessage());
            }
        }

        return $uploadedPhotos;
    }

    private function uploadVideos(array $videoPaths, ?int $groupId = null): array
    {
        $uploadedVideos = [];

        foreach ($videoPaths as $videoPath) {
            try {
                $saveResponse = Http::timeout(360)->get('https://api.vk.com/method/video.save', [
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

    private function uploadDocuments(array $documentPaths, ?int $groupId = null): array
    {
        $uploadedDocs = [];

        foreach ($documentPaths as $docPath) {
            try {
                // Получение сервера для загрузки документов на стену группы
                $uploadServerResponse = Http::get('https://api.vk.com/method/docs.getWallUploadServer', [
                    'group_id' => $groupId,
                    'access_token' => (new VkAuthService())->getToken()->access_token,
                    'v' => '5.131',
                ]);

                $uploadServerData = $uploadServerResponse->json();
                Log::info($uploadServerData);
                if (!isset($uploadServerData['response']['upload_url'])) {
                    throw new \Exception('Не удалось получить сервер для загрузки документа: ' . $docPath);
                }
                $uploadUrl = $uploadServerData['response']['upload_url'];

                // Загрузка документа на сервер
                $uploadResponse = Http::attach(
                    'file',
                    file_get_contents($docPath),
                    basename($docPath)
                )->post($uploadUrl);

                $uploadData = $uploadResponse->json();
                if (!isset($uploadData['file'])) {
                    throw new \Exception('Не удалось загрузить документ: ' . $docPath);
                }

                // Сохранение документа
                $saveResponse = Http::get('https://api.vk.com/method/docs.save', [
                    'file' => $uploadData['file'],
                    'access_token' => (new VkAuthService())->getToken()->access_token,
                    'v' => '5.131',
                ]);

                $saveData = $saveResponse->json();

                // Проверка наличия данных о документе
                if (!isset($saveData['response']['doc'])) {
                    throw new \Exception('Не удалось сохранить документ: ' . $docPath);
                }

                // Получение данных о документе
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
        $vk = new VKApiClient();

        $album = (new VkAlbumService($vk))->createAlbum($title);
        $uploadServer = (new VkAlbumService($vk))->getServerForUploadImages($album['id'], env('PUBLIC_ID'));
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

