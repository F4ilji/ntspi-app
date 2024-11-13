<?php

namespace App\Services\VK;

use App\Services\VK\Album\VkAlbumService;
use App\Services\VK\Wall\VkWallService;
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
    }

    public function getPosts(int $count = 10)
    {
        return $this->wallService->getPosts($count);
    }

    public function getPostById(int $id)
    {
        return $this->wallService->getPostById($id);
    }

    public function createPost(string $title, string $message, array $images = [], int|null $publish_date = null)
    {
        $from_group = 1;
        $album_attachment = '';
        if ($images !== []) {
            $album = $this->createAlbum($title, $images);
            $album_attachment = $this->createAlbumAttachmentParam($album['id']);
        }

        return $this->wallService->createPost($message, $from_group, $album_attachment, $publish_date);
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

}