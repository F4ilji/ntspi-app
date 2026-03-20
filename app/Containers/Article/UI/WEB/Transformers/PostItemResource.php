<?php

namespace App\Containers\Article\UI\WEB\Transformers;

use App\Ship\Resources\JsonResource;
use Carbon\Carbon;

class PostItemResource extends JsonResource
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
            'content' => $this->content,
            'is_published' => $this->is_published,
            'category' => $this->category,
            'tags' => TagResource::collection($this->tags()->get()),
            'authors' => $this->authors,
            'gallery' => $this->images,
            'reading_time' => $this->reading_time,
            'created_post' => Carbon::parse($this->publish_at)
                ->locale('ru')
                ->translatedFormat(
                    Carbon::parse($this->publish_at)->isCurrentYear()
                        ? 'd M'
                        : 'd M Y' . ' г.'
                )
        ];
    }
}
