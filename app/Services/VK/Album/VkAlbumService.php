<?php

namespace App\Services\VK\Album;

use App\Services\VK\VkAuthService;
use CURLFile;
use GuzzleHttp\Client;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Log;
use VK\Client\VKApiClient;

class VkAlbumService
{
    private VKApiClient $vk;
    private VkAuthService $vkAuthService;

    private string $wallToken;
    private string $serviceToken;
    private string $publicId;

    public function __construct(VKApiClient $vk) {
        $this->vk = $vk;
        $this->vkAuthService = new VkAuthService();
        $this->serviceToken = config('services.vk.service_key');
        $this->publicId = config('services.vk.public_id');
    }

    public function getServerForUploadImages($album_id, $group_id)
    {
        return $this->vk->photos()->getUploadServer(
            $this->vkAuthService->getToken()->access_token,
            array(
            'album_id' => $album_id,
            'group_id' => $group_id,
            )
        );
    }

    public function getServerForUploadImagesOnWall($group_id)
    {
        return $this->vk->photos()->getWallUploadServer(
            $this->vkAuthService->getToken()->access_token,
            array(
                'group_id' => $group_id,
            )
        );
    }


    public function uploadImagesToUploadServer($uploadUrl, $images)
    {
        // Массив для хранения локальных путей к загруженным изображениям
        $localFiles = [];

        // Загрузка изображений из URL
        foreach ($images as $imageUrl) {
            $fullPath = public_path($imageUrl);
            $localFile = tempnam(sys_get_temp_dir(), 'img_') . '.webp';
            file_put_contents($localFile, file_get_contents($fullPath));
            $localFiles[] = $localFile;
        }


        // Подготовка данных для отправки
        $postFields = [];
        foreach ($localFiles as $index => $localFile) {
            // Добавляем файл в массив
            $postFields['file' . ($index + 1)] = new CURLFile($localFile);
        }

        // Инициализация cURL
        $ch = curl_init();

        // Установка параметров cURL
        curl_setopt($ch, CURLOPT_URL, $uploadUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

        // Выполнение запроса
        $response = curl_exec($ch);

        curl_close($ch);

        // Проверка на ошибки
        if (curl_errno($ch)) {
            return 'Ошибка cURL: ' . curl_error($ch);
        }

        // Закрытие cURL

        // Обработка ответа
        $responseData = json_decode($response, true);

        // Удаление временных файлов
        foreach ($localFiles as $localFile) {
            unlink($localFile);
        }

        return $responseData;
    }

    public function saveImagesToUploadServer($albumId, $server, $photosList, $hash)
    {
        // URL для запроса
        $url = 'https://api.vk.ru/method/photos.save';

        // Подготовка данных для отправки
        $postFields = [
            'album_id' => $albumId,
            'server' => $server,
            'group_id' => config('services.vk.public_id'),
            'photos_list' => $photosList, // Преобразуем массив в JSON-строку
            'hash' => $hash,
            'access_token' => $this->vkAuthService->getToken()->access_token,
            'v' => '5.131', // Версия API
        ];

        // Инициализация cURL
        $ch = curl_init();

        // Установка параметров cURL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

        // Выполнение запроса
        $response = curl_exec($ch);

        // Проверка на ошибки
        if (curl_errno($ch)) {
            return 'Ошибка cURL: ' . curl_error($ch);
        }

        // Закрытие cURL
        curl_close($ch);

        // Обработка ответа
        $responseData = json_decode($response, true);

        return $responseData;
    }
    public function createAlbum(string $title)
    {
        try {
            return $this->vk->photos()->createAlbum($this->vkAuthService->getToken()->access_token, array(
                'title' => $title,
                'group_id' => $this->publicId,
                'privacy' => 0,

            ));
        } catch (\Exception $e) {
            Log::error('Ошибка при создании альбома: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Не удалось создать альбом: ' . $e->getMessage(),
            ];
        }
    }

    public function deleteAlbum(int $album_id, int $group_id)
    {
        try {
            return $this->vk->photos()->deleteAlbum($this->vkAuthService->getToken()->access_token, array(
                'album_id' => $album_id,
                'group_id' => $group_id,
            ));
        } catch (\Exception $e) {
            Log::error('Ошибка при удалении альбома: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Не удалось удалить альбом: ' . $e->getMessage(),
            ];
        }
    }
}