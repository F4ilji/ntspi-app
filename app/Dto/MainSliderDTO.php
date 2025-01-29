<?php

namespace App\Dto;

use Carbon\Carbon;

class MainSliderDTO
{
    public function __construct(
        public ?string $title,
        public ?string $content,
        public ?string $image,
        public ?string $link,
        public ?string $link_text,
        public ?string $color_theme,
        public ?bool $is_active,
        public ?Carbon $start_time,
        public ?Carbon $end_time,
        public ?int $sort,
    ) {}

    // Опционально: метод для создания DTO из массива
    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'] ?? null,
            content: $data['content'] ?? null,
            image: $data['image'] ?? null,
            link: $data['link'] ?? null,
            link_text: $data['link_text'] ?? null,
            color_theme: $data['color_theme'] ?? 'white',
            is_active: $data['is_active'] ?? true,
            start_time: isset($data['start_time']) ? Carbon::parse($data['start_time']) : null,
            end_time: isset($data['end_time']) ? Carbon::parse($data['end_time']) : null,
            sort: $data['sort'] ?? null,
        );
    }

    // Опционально: метод для преобразования DTO в массив
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'image' => $this->image,
            'link' => $this->link,
            'link_text' => $this->link_text,
            'color_theme' => $this->color_theme,
            'is_active' => $this->is_active,
            'start_time' => $this->start_time?->toDateTimeString(),
            'end_time' => $this->end_time?->toDateTimeString(),
            'sort' => $this->sort,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}