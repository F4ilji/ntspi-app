<?php

namespace App\Services\Filament\Domain\Posts;

use App\Containers\Widget\Models\Slide;
use App\Dto\MainSliderDTO;
use App\Containers\Article\Models\Post;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class PostSliderService
{
    public function __construct(
        private readonly MainSliderDTO $dto,
        private readonly Model $post,
    ) {}

    /**
     * Create a new MainSlider record associated with the post.
     *
     * @throws \Exception
     */
    public function create(): void
    {
        try {
            $post = Post::find($this->post->id);
            $slide = new Slide([
                'title' => $this->dto->title,
                'content' => $this->dto->content,
                'image' => $this->dto->image,
                'link' => $this->generatePostLink(),
                'settings' => $this->dto->settings,
                'color_theme' => $this->dto->color_theme,
                'is_active' => $this->dto->is_active,
                'start_time' => $this->dto->start_time,
                'end_time' => $this->dto->end_time,
                'slider_id' => $this->dto->slider_id,
            ]);
            $post->slide()->save($slide);

            Log::info('Slide created successfully', ['postTitle' => $this->post->title]);
        } catch (\Exception $e) {
            Log::error('Failed to create slide', [
                'postTitle' => $this->post->title,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Update an existing MainSlider record associated with the post.
     *
     * @throws \Exception
     */
    public function update(): void
    {
        try {
            $post = Post::find($this->post->id);
            $post->slide()->update([
                'title' => $this->dto->title,
                'content' => $this->dto->content,
                'image' => $this->dto->image,
                'link' => $this->generatePostLink(),
                'settings' => $this->dto->settings,
                'color_theme' => $this->dto->color_theme,
                'is_active' => $this->dto->is_active,
                'start_time' => $this->dto->start_time,
                'end_time' => $this->dto->end_time,
                'slider_id' => $this->dto->slider_id,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update MainSlider', [
                'postTitle' => $this->post->title,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Generate the link for the post.
     *
     * @return string
     */
    private function generatePostLink(): string
    {
        return parse_url(route('client.post.show', $this->post->slug), PHP_URL_PATH);
    }
}
