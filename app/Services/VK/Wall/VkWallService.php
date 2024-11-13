<?php

namespace App\Services\VK\Wall;

use App\Services\VK\VkAuthService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Http;
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
        $this->vkAuthService = new VkAuthService();

    }

    public function getPosts(int $count)
    {
        return $this->vk->wall()->get($this->serviceToken, array(
            'owner_id' => '-'. $this->publicId,
            'domain' => $this->publicDomain,
            'count' => $count
        ));
    }

    public function getPostById(int $id)
    {
        $post = $this->vk->wall()->getById($this->serviceToken, array(
            'posts' => '-'. $this->publicId . '_' . $id,
        ));
        return $post[0];
    }



    public function createPost(string $message, int $from_group, string $attachments = '', int|null $publish_date = null)
    {
        $params = [
            'owner_id' => '-' . $this->publicId,
            'from_group' => $from_group,
            'message' => $message,
            'attachments' => $attachments,
            'access_token' => $this->wallToken,
            'publish_date' => $publish_date,
            'v' => '5.131',
        ];

        try {
            $response = Http::asForm()->post('https://api.vk.com/method/wall.post', $params);

            if ($response->successful() && isset($response['response'])) {
                return [
                    'success' => true,
                    'post_id' => $response['response']['post_id'],
                ];
            } else {
                throw new \Exception('Ошибка API: ' . json_encode($response->json()));
            }

        } catch (\Exception $e) {
            Log::error('Ошибка при создании поста: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Не удалось создать пост: ' . $e->getMessage(),
            ];
        }
    }

    public function updatePost(int $post_id, string $message = '', int $from_group = 1, string $attachments = '', int|null $publish_date = null)
    {
        if (empty($message) && empty($attachments)) {
            return [
                'success' => false,
                'message' => 'Необходимо указать либо сообщение, либо вложения.',
            ];
        }


        try {
            return $this->vk->wall()->edit(
                $this->vkAuthService->getToken()->access_token,
                array(
                    'owner_id' => '-' . $this->publicId,
                    'post_id' => $post_id,
                    'message' => $message,
                    'attachments' => $attachments,
                    'publish_date' => $publish_date,
                ),
            );
        } catch (\Exception $e) {
            Log::error('Ошибка при обновлении новости SDK: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Не удалось обновить новость SDK: ' . $e->getMessage(),
            ];
        }
    }
    public function generateAttachmentsParams(string $attachmentType, int $attachmentId)
    {
        $pubic_id = env('PUBLIC_ID');
        switch ($attachmentType) {
            case 'album': {
                return "album-{$pubic_id}_{$attachmentId}";
            }
            case 'doc': {

            }
        }
    }
}