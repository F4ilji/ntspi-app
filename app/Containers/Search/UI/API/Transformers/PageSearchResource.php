<?php

namespace App\Containers\Search\UI\API\Transformers;

use App\Ship\Resources\JsonResource;


class PageSearchResource extends JsonResource
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
            'path' => $this->path,
            'is_url' => $this->is_url,
            'section' => $this->section ? $this->section->title : null,
        ];
    }
}
