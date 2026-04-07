<?php

namespace App\Containers\Dashboard\Actions\Posts;

use App\Containers\Article\Enums\PostStatus;
use App\Containers\Article\Models\Post;
use App\Services\Filament\Domain\Posts\PostDataProcessor;
use App\Services\Filament\Domain\Posts\PostNotificationService;
use App\Services\Filament\Domain\Posts\PostSliderService;
use App\Services\Filament\Domain\Posts\VkPostPublisher;
use App\Dto\MainSliderDTO;
use Illuminate\Support\Carbon;

class PublishPostAction
{
    public function __construct(
        private readonly PostDataProcessor $postDataProcessor,
    ) {}

    /**
     * Публикует черновик поста
     *
     * @param Post $post Пост для публикации
     * @param bool $publishToVk Публиковать ли в VK
     * @return Post Опубликованный пост
     */
    public function run(Post $post, bool $publishToVk = true): Post
    {
        // Обновляем данные поста
        $updatedData = $this->postDataProcessor->processUpdate([
            ...$post->toArray(),
            'status' => PostStatus::PUBLISHED,
            'publish_setting' => [
                'publish_after' => false,
                'publish_at' => null,
            ],
        ]);

        $post->update($updatedData);

        // Отправляем уведомления
        $this->sendNotifications($post);

        // Публикуем в VK, если указано
        if ($publishToVk) {
            $this->publishToVk($post);
        }

        // Обновляем слайд, если есть
        $this->updateSlide($post);

        return $post->fresh();
    }

    /**
     * Отправляет уведомления о публикации
     */
    private function sendNotifications(Post $post): void
    {
        (new PostNotificationService())->send($post);
    }

    /**
     * Публикует пост в VK
     */
    private function publishToVk(Post $post): void
    {
        // Публикуем в VK по умолчанию (аналогично Filament, где есть чекбокс)
        (new VkPostPublisher())->publish(['vk' => true], $post);
    }

    /**
     * Обновляет слайд для поста
     */
    private function updateSlide(Post $post): void
    {
        // Если у поста уже есть слайд, обновляем его статус
        if ($post->slide) {
            $post->slide->update([
                'is_active' => true,
                'start_time' => $post->publish_at ?? Carbon::now(),
            ]);
        }
        // Если слайда нет, но есть превью - создаём новый
        elseif ($post->preview) {
            $slideData = [
                'is_active' => true,
                'start_time' => $post->publish_at ?? Carbon::now(),
                'title' => $post->title,
                'description' => $post->preview_text,
                'image' => $post->preview,
            ];

            $sliderDTO = MainSliderDTO::fromArray($slideData);
            (new PostSliderService($sliderDTO, $post))->create();
        }
    }
}
