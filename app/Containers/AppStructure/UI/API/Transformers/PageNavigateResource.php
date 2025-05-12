<?php

namespace App\Containers\AppStructure\UI\API\Transformers;


use App\Ship\Resources\JsonResource;

class PageNavigateResource extends JsonResource
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
            'path' => $this->path,
            'is_url' => $this->is_url,
            'icon' => $this->icon
        ];
    }
}
