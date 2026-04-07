<?php

namespace App\Containers\Dashboard\Tasks\Posts;

use App\Containers\Article\Enums\PostStatus;
use App\Containers\Article\Models\Post;
use App\Services\Filament\Domain\Posts\PostNotificationService;

class SendPostNotificationTask
{
    /**
     * Отправляет уведомления о статусе поста
     *
     * @param Post $post
     * @param string|null $newStatus Новый статус поста
     * @param bool $isCreation Флаг создания (true) или обновления (false)
     * @return void
     */
    public function run(Post $post, ?string $newStatus = null, bool $isCreation = false): void
    {
        $notificationService = new PostNotificationService();

        // При создании всегда отправляем уведомление
        if ($isCreation) {
            $notificationService->send($post);
            return;
        }

        // При обновлении отправляем только при смене статуса
        $status = $newStatus ?? $post->status;

        if ($status instanceof PostStatus) {
            if ($status === PostStatus::PUBLISHED) {
                $notificationService->sendSuccessNotification($post);
            } elseif ($status === PostStatus::REJECTED) {
                $notificationService->sendDeniedNotification($post);
            }
        }
    }
}
