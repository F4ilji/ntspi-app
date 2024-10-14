<?php

namespace App\Services\VK\Wall;

use Illuminate\Support\Facades\Log;
use VK\Client\VKApiClient;

class VkWallService
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

    public function getPosts(int $count)
    {
        return $this->vk->wall()->get($this->serviceToken, array(
            'owner_id' => '-'. $this->publicId,
            'domain' => $this->publicDomain,
            'count' => $count
        ));
    }

    public function createPost(string $message, int $from_group, string $attachments = '')
    {
        try {
            return $this->vk->wall()->post($this->wallToken, array(
                'owner_id' => '-' . $this->publicId,
                'from_group' => $from_group,
                'message' => $message,
                'attachments' => $attachments
            ));
        } catch (\Exception $e) {
            Log::error('Ошибка при создании поста: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Не удалось создать пост: ' . $e->getMessage(),
            ];
        }
    }
}