<?php

namespace App\Containers\Dashboard\Tasks\Posts;

use App\Containers\Article\Models\Post;
use App\Dto\MainSliderDTO;
use App\Services\Filament\Domain\Posts\PostSliderService;
use Illuminate\Support\Carbon;

class HandlePostSliderTask
{
    /**
     * Обрабатывает слайдер для поста (создание/обновление/удаление)
     *
     * @param Post $post
     * @param array $slideData Данные слайдера
     * @param bool $isCreate Создание или обновление
     * @return void
     */
    public function run(Post $post, array $slideData, bool $isCreate = false): void
    {
        // Если слайдер не передан или slider_id null - ничего не делаем
        if (empty($slideData) || empty($slideData['slider_id'])) {
            return;
        }

        // Если нужно удалить слайдер
        if (isset($slideData['slider_id']) && $slideData['slider_id'] === null) {
            $post->slide?->delete();
            return;
        }

        $data = [
            'is_active' => $slideData['is_active'] ?? false,
            'start_time' => $post->publish_at ?? Carbon::now(),
            'title' => $slideData['title'] ?? $post->title,
            'description' => $slideData['content'] ?? $post->preview_text,
            'image' => $slideData['image']['url'] ?? null,
            'color_theme' => $slideData['color_theme'] ?? '#ffffff',
        ];

        // Добавляем настройки слайда
        if (isset($slideData['settings'])) {
            $data = array_merge($data, $slideData['settings']);
        }

        $sliderDTO = MainSliderDTO::fromArray($data);
        $sliderService = new PostSliderService($sliderDTO, $post);

        // Обновляем или создаем слайдер
        if ($post->slide && !$isCreate) {
            $sliderService->update();
        } else {
            $sliderService->create();
        }
    }
}
