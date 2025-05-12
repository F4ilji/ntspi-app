<?php

namespace App\Containers\InstituteStructure\UI\WEB\Transformers;

use App\Containers\User\UI\WEB\Transformers\PersonDivisionPreviewResource;
use App\Ship\Resources\JsonResource;

class DivisionResource extends JsonResource
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
            'workers' => PersonDivisionPreviewResource::collection($this->whenLoaded('workers')),
            'description' => $this->description,
        ];
    }
}
