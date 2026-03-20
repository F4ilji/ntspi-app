<?php

namespace App\Containers\Search\UI\API\Transformers;

use Carbon\Carbon;
use App\Ship\Resources\JsonResource;


class PostSearchResource extends JsonResource
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
            'created_post' => Carbon::parse($this->publish_at)->diffforhumans(),
        ];
    }
}
