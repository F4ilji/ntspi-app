<?php

namespace App\Containers\Widget\UI\API\Transformers;

use App\Ship\Resources\JsonResource;
use Carbon\Carbon;

class PostThumbnailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'preview_text' => $this->preview_text,
            'category' => $this->category,
            'authors' => $this->authors,
            'preview' => $this->preview,
            'created_post' => Carbon::parse($this->publish_at)->diffforhumans(),
        ];
    }
}
