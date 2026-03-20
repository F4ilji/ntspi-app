<?php

namespace App\Dto;

use Carbon\Carbon;

class MainSliderDTO
{
    public function __construct(
        public ?string $title,
        public ?string $content,
        public ?array $image,
        public ?string $link,
        public ?array $settings,
        public ?string $color_theme,
        public ?bool $is_active,
        public ?Carbon $start_time,
        public ?Carbon $end_time,
        public ?int $sort,
        public int $slider_id,
    ) {}

    // Опционально: метод для создания DTO из массива
    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'] ?? null,
            content: $data['content'] ?? null,
            image: $data['image'] ?? null,
            link: $data['link'] ?? null,
            settings: $data['settings'] ?? null,
            color_theme: $data['color_theme'] ?? 'white',
            is_active: $data['is_active'] ?? true,
            start_time: isset($data['start_time']) ? Carbon::parse($data['start_time']) : null,
            end_time: isset($data['end_time']) ? Carbon::parse($data['end_time']) : null,
            sort: $data['sort'] ?? null,
            slider_id: $data['slider_id'],
        );
    }

    // Опционально: метод для преобразования DTO в массив
    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'content' => $this->content,
            'image' => $this->image,
            'link' => $this->link,
            'settings' => $this->settings,
            'color_theme' => $this->color_theme,
            'is_active' => $this->is_active,
            'start_time' => $this->start_time?->toDateTimeString(),
            'end_time' => $this->end_time?->toDateTimeString(),
            'sort' => $this->sort,
            'slider_id' => $this->slider_id,
        ];
    }
}