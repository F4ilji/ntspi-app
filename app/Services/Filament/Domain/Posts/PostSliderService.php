<?php

namespace App\Services\Filament\Domain\Posts;

use App\Dto\MainSliderDTO;
use App\Models\MainSlider;
use Illuminate\Support\Facades\Log;

class PostSliderService
{
    public function __construct(
        readonly private MainSliderDTO $dto,
        readonly private string $postSlug,
    ){}

    public function create(): void
    {
        try {
            MainSlider::create([
                'title' => $this->dto->title,
                'content' => $this->dto->content,
                'image' => $this->dto->image,
                'link' => parse_url(route('client.post.show', $this->postSlug), PHP_URL_PATH),
                'link_text' => $this->dto->link_text,
                'is_active' => $this->dto->is_active,
                'color_theme' => $this->dto->color_theme,
                'start_time' => $this->dto->start_time,
                'end_time' => $this->dto->end_time,
            ]);

            Log::info('MainSlider created successfully', ['postSlug' => $this->postSlug]);
        } catch (\Exception $e) {
            Log::error('Failed to create MainSlider', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function update(): void
    {
        try {
            MainSlider::update([
                'title' => $this->dto->title,
                'content' => $this->dto->content,
                'image' => $this->dto->image,
                'link' => parse_url(route('client.post.show', $this->postSlug), PHP_URL_PATH),
                'link_text' => $this->dto->link_text,
                'is_active' => $this->dto->is_active,
                'color_theme' => $this->dto->color_theme,
                'start_time' => $this->dto->start_time,
                'end_time' => $this->dto->end_time,
            ]);

            Log::info('MainSlider updated successfully', ['postSlug' => $this->postSlug]);
        } catch (\Exception $e) {
            Log::error('Failed to update MainSlider', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}