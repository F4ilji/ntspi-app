<?php

namespace App\Services\VK;

use App\Services\VK\Album\VkAlbumService;
use App\Services\VK\Wall\VkWallService;
use VK\Client\VKApiClient;

class VkService
{
    protected VkWallService $wallService;
    protected VkAlbumService $albumService;
    public function __construct(VKApiClient $vk) {
        $this->wallService = new VkWallService($vk);
        $this->albumService = new VkAlbumService($vk);
    }

    public function getPosts(int $count = 10)
    {
        return $this->wallService->getPosts($count);
    }

    public function createPost(string $message, int $from_group = 1)
    {
        return $this->wallService->createPost($message, $from_group);
    }

    public function createAlbum(string $title)
    {
        return $this->albumService->createAlbum($title);
    }

}