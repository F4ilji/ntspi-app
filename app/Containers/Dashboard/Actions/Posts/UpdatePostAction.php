<?php

namespace App\Containers\Dashboard\Actions\Posts;

use App\Containers\Article\Models\Post;
use App\Containers\Dashboard\Tasks\Posts\HandlePostSliderTask;
use App\Containers\Dashboard\Tasks\Posts\SendPostNotificationTask;
use App\Containers\Dashboard\Tasks\Posts\UpdatePostTask;

class UpdatePostAction
{
    public function __construct(
        private readonly UpdatePostTask $updatePostTask,
        private readonly HandlePostSliderTask $handleSliderTask,
        private readonly SendPostNotificationTask $sendNotificationTask,
    ) {}

    /**
     * Обновляет существующий пост
     *
     * @param Post $post Пост для обновления
     * @param array $data Данные поста из формы
     * @return Post Обновленный пост
     */
    public function run(Post $post, array $data): Post
    {
        // Извлекаем данные слайдера и публикации
        $slideData = $data['slide'] ?? [];

        // Удаляем служебные данные перед обновлением
        unset($data['slide'], $data['publication']);

        // Обновляем пост (Task)
        $post = $this->updatePostTask->run($post, $data);

        // Обрабатываем слайдер (Task)
        $this->handleSliderTask->run($post, $slideData, false);

        // Отправляем уведомления (Task)
        $this->sendNotificationTask->run($post, $data['status'] ?? null, false);

        return $post;
    }
}
