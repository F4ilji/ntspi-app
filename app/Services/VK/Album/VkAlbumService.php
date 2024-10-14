<?php

namespace App\Services\VK\Album;

use Illuminate\Support\Facades\Log;
use VK\Client\VKApiClient;

class VkAlbumService
{
    private VKApiClient $vk;
    private string $wallToken;
    private string $serviceToken;
    private string $publicId;
    private string $publicDomain;

    public function __construct(VKApiClient $vk) {
        $this->vk = $vk;
        $this->wallToken = env('WALL_ACCESS_VK_TOKEN');
        $this->serviceToken = env('SERVICE_ACCESS_VK_KEY');
        $this->publicId = env('PUBLIC_ID');
        $this->publicDomain = env('PUBLIC_DOMAIN');
    }

    public function getServerForUploadImages()
    {
        return $this->vk->photos()->getUploadServer($this->wallToken, array(
            ''
        ));
    }

    public function createAlbum(string $title)
    {
        try {
            return $this->vk->photos()->createAlbum($this->wallToken, array(
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

}