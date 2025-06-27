<?php

namespace App\Services\VK;

use App\Services\VK\Album\VkAlbumService;
use App\Services\VK\Wall\VkWallService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use VK\Client\VKApiClient;

class VkService
{
    protected VkWallService $wallService;
    protected VkAlbumService $albumService;
    protected int $public_id;
    public function __construct() {
        $vk = new VKApiClient();
        $this->wallService = new VkWallService($vk);
        $this->albumService = new VkAlbumService($vk);
        $this->public_id = env('PUBLIC_ID');
        $this->vkAuthService = new VkAuthService();
    }

    public function getPosts(int $count = 10)
    {
        return $this->wallService->getPosts($count);
    }

    public function getPostById(int $id)
    {
        return $this->wallService->getPostById($id);
    }

//    public function createPost(string $title, string $message, array $images = [], array $videos = [], int|null $publish_date = null)
//    {
//        $from_group = 1;
//        $attachments = [];
//
//        if (!empty($images)) {
//            if (count($images) <= 10) {
//                $attachments[] = $this->prepareWallPhotos($images);
//            }
//            else{
//                $album = $this->createAlbum($title, $images);
//                $attachments[] = $this->createAlbumAttachmentParam($album['id']);
//            }
//        }
//
//        $attachmentString = implode(',', $attachments);
//
//        return $this->wallService->createPost($message, $from_group, $attachmentString, $publish_date);
//    }

    public function createPost(string $title, string $message, array $images = [], array $videos = [], int|null $publish_date = null)
    {
        $from_group = 1;
        $attachments = [];

        if (!empty($images)) {
            if (count($images) <= 10) {
                $attachments[] = $this->prepareWallPhotos($images);
            } else {
                $attachments[] = $this->prepareWallPhotos(array_slice($images, 0, 10));

                $album = $this->createAlbum($title, $images);
                if (isset($album['id'])) {
                    $albumLink = "https://vk.com/album-{$this->public_id}_{$album['id']}";
                    $message .= "\n[{$albumLink}|Ссылка на все фотографии]";
                } else {
                    Log::error('Не удалось создать альбом');
                }
            }
        }

        $attachmentString = implode(',', $attachments);

        return $this->wallService->createPost($message, $from_group, $attachmentString, $publish_date);
    }

    public function updatePost(int $id, string $title, string $message, array $images = [], int|null $publish_date = null)
    {
        $from_group = 1;
        $vk_post = $this->wallService->getPostById($id);
        $album_attachment = '';
        if ($vk_post['attachments']) {
            if ($this->getAlbumAttachment($vk_post['attachments'])) {
                $album = $this->getAlbumAttachment($vk_post['attachments']);
                $this->albumService->deleteAlbum($album['album']['id'], $this->public_id);
            }
        }
        if ($images !== []) {
            $album = $this->createAlbum($title, $images);
            $album_attachment = $this->createAlbumAttachmentParam($album['id']);
        }

        return $this->wallService->updatePost($id, $message, $from_group, $album_attachment, $publish_date);
    }

    public function deletePost(int $post_id){
        $postRelation = DB::table('posts_vk_posts')->select()->where('post_id', $post_id)->first();
        $post_id = $postRelation->vk_post_id;
        $vk_post = $this->getPostById($post_id);

        $pattern = '/\[https:\/\/vk.com\/album-?[0-9]+_([0-9]+)\|.*\]/';
        preg_match($pattern, $vk_post['text'], $matches);
        if (isset($matches[1])) {
            $album_id = $matches[1];
            $this->albumService->deleteAlbum($album_id, $this->public_id);
            $this->wallService->deletePost($vk_post['id']);
        } else {
            $this->wallService->deletePost($vk_post['id']);
        }
    }

    public function createAlbum(string $title, $images)
    {
        $album = $this->albumService->createAlbum($title);
        $uploadServer = $this->albumService->getServerForUploadImages($album['id'], env('PUBLIC_ID'));
        foreach (array_chunk($images, 4) as $images_slice) {
            $images_data = $this->albumService->uploadImagesToUploadServer($uploadServer['upload_url'], $images_slice);
            $this->albumService->saveImagesToUploadServer(
                $images_data['aid'],
                $images_data['server'],
                $images_data['photos_list'],
                $images_data['hash']
            );
        }
        return $album;
    }

    private function createAlbumAttachmentParam(int $attachmentId)
    {
        return $this->wallService->generateAttachmentsParams('album', $attachmentId);
    }

    private function getAlbumAttachment(array $attachments)
    {
        $data = collect($attachments);
        $filteredAttachment = $data->filter(function($attachment) {
            return $attachment['type'] === 'album';
        });
        return (isset($filteredAttachment[0]) ? $filteredAttachment[0] : null);
    }

    private function prepareWallPhotos(array $images){
        $baseUrl = config('app.url');
        try {
            $imagePaths = array_map(function($img) use ($baseUrl) {
                return $baseUrl . $img;
            }, $images);

            $photos = $this->uploadWallPhotos($imagePaths, env('PUBLIC_ID'));

            return implode(',', array_map(function($photo) {
                return "photo{$photo['owner_id']}_{$photo['id']}";
            }, $photos));
        } catch (Exception $e) {
            Log::error("Ошибка: " . $e->getMessage());
        }
    }

    private function prepareVideos(array $videos)
    {
        $baseUrl = config('app.url');
        $videoPaths = array_map(function($vid) use ($baseUrl) {
            return $baseUrl . $vid;
        }, $videos);
        return $this->uploadVideos($videoPaths, env('PUBLIC_ID')); // Возвращает массив, например: ['video123_456', 'video789_012']
    }

    private function uploadVideos(array $videoPaths, ?int $groupId = null): array
    {
        $uploadedVideos = [];

        foreach ($videoPaths as $videoPath) {
            try {
                // Шаг 1: Получение сервера для загрузки
                $saveResponse = Http::get('https://api.vk.com/method/video.save', [
                    'group_id' => $groupId,
                    'access_token' => $this->vkAuthService->getToken()->access_token,
                    'v' => '5.131',
                ]);

                $saveData = $saveResponse->json();
                if (!isset($saveData['response']['upload_url'])) {
                    throw new Exception('Не удалось получить сервер для загрузки видео: ' . $videoPath);
                }
                $uploadUrl = $saveData['response']['upload_url'];

                // Шаг 2: Загрузка видео на сервер
                $uploadResponse = Http::attach(
                    'video_file',
                    file_get_contents($videoPath),
                    basename($videoPath)
                )->post($uploadUrl);

                $uploadData = $uploadResponse->json();
                if (!isset($uploadData['video_id']) || !isset($uploadData['owner_id'])) {
                    throw new Exception('Не удалось загрузить видео: ' . $videoPath);
                }


                $attachment = "video{$uploadData['owner_id']}_{$uploadData['video_id']}";
                $uploadedVideos[] = $attachment;
            } catch (Exception $e) {
                Log::error("Ошибка при загрузке видео {$videoPath}: " . $e->getMessage());
            }
        }

        return $uploadedVideos;
    }

    private function uploadWallPhotos(array $imagePaths, ?int $groupId = null): array
    {
        $uploadedPhotos = [];

        foreach ($imagePaths as $imagePath) {
            try {
                $uploadServerResponse = Http::get('https://api.vk.com/method/photos.getWallUploadServer', [
                    'group_id' => $groupId,
                    'access_token' => $this->vkAuthService->getToken()->access_token,
                    'v' => '5.131',
                ]);

                $uploadServerData = $uploadServerResponse->json();
                if (!isset($uploadServerData['response']['upload_url'])) {
                    throw new Exception('Не удалось получить сервер для загрузки для изображения: ' . $imagePath);
                }
                $uploadUrl = $uploadServerData['response']['upload_url'];

                // Шаг 2: Загрузка фотографии на сервер
                $uploadResponse = Http::attach(
                    'photo',
                    file_get_contents($imagePath),
                    basename($imagePath)
                )->post($uploadUrl);

                $uploadData = $uploadResponse->json();
                if (!isset($uploadData['photo']) || !isset($uploadData['server']) || !isset($uploadData['hash'])) {
                    throw new Exception('Не удалось загрузить фотографию: ' . $imagePath);
                }

                // Шаг 3: Сохранение фотографии
                $saveResponse = Http::get('https://api.vk.com/method/photos.saveWallPhoto', [
                    'group_id' => $groupId,
                    'photo' => $uploadData['photo'],
                    'server' => $uploadData['server'],
                    'hash' => $uploadData['hash'],
                    'access_token' => $this->vkAuthService->getToken()->access_token,
                    'v' => '5.131',
                ]);

                $saveData = $saveResponse->json();
                if (!isset($saveData['response'][0])) {
                    throw new Exception('Не удалось сохранить фотографию: ' . $imagePath);
                }

                // Добавляем данные о загруженной фотографии в массив
                $uploadedPhotos[] = $saveData['response'][0];
                Log::info("Фотография успешно загружена. ID: " . $saveData['response'][0]['id']);
            } catch (Exception $e) {
                // Логируем ошибку, но продолжаем загрузку остальных изображений
                Log::error("Ошибка при загрузке изображения {$imagePath}: " . $e->getMessage());
            }
        }

        return $uploadedPhotos; // Возвращаем массив с данными о загруженных фотографиях
    }

}