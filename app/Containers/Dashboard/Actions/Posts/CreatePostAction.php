<?php

namespace App\Containers\Dashboard\Actions\Posts;

use App\Containers\Article\Models\Post;
use App\Containers\Dashboard\Tasks\Posts\CreatePostTask;
use App\Containers\Dashboard\Tasks\Posts\HandlePostSliderTask;
use App\Containers\Dashboard\Tasks\Posts\PublishPostToVkTask;
use App\Containers\Dashboard\Tasks\Posts\SendPostNotificationTask;

class CreatePostAction
{
    public function __construct(
        private readonly CreatePostTask $createPostTask,
        private readonly HandlePostSliderTask $handleSliderTask,
        private readonly SendPostNotificationTask $sendNotificationTask,
        private readonly PublishPostToVkTask $publishToVkTask,
    ) {}

    /**
     * Создает новый пост
     *
     * @param array $data Данные поста из формы
     * @return Post Созданный пост
     */
    public function run(array $data): Post
    {
        // Извлекаем данные слайдера и публикации
        $slideData = $data['slide'] ?? [];
        $shouldPublishToVk = $data['publication']['vk'] ?? false;

        // Удаляем служебные данные перед созданием
        unset($data['slide'], $data['publication']);

        // Создаем пост (Task)
        $post = $this->createPostTask->run($data);

        // Обрабатываем слайдер (Task)
        $this->handleSliderTask->run($post, $slideData, true);

        // Отправляем уведомления (Task)
        $this->sendNotificationTask->run($post, null, true);

        // Публикуем в VK (Task)
        $this->publishToVkTask->run($post, $shouldPublishToVk, false);

        return $post;
    }
}
