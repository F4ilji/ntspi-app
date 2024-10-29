<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostThumbnailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
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
