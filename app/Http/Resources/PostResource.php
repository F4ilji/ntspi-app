<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
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
            'content' => $this->content,
            'is_published' => $this->is_published,
            'category' => $this->category,
            'tags' => ClientTagResource::collection($this->tags()->get()),
            'authors' => $this->authors,
            'gallery' => $this->images,
            'reading_time' => $this->reading_time,
            'created_post' => Carbon::parse($this->publish_at)->diffforhumans(),
        ];
    }
}
