<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientPostListResource extends JsonResource
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
            'content' => $this->content,
            'category' => $this->category,
            'authors' => $this->authors,
            'preview' => $this->preview,
            'reading_time' => $this->reading_time,
            'created_post' => Carbon::parse($this->publish_at)->diffforhumans(),
        ];
    }
}
