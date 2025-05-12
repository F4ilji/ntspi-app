<?php

namespace App\Containers\Article\UI\WEB\Transformers;

use App\Ship\Resources\JsonResource;
use Carbon\Carbon;

class PostListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \App\Ship\Requests\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
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
