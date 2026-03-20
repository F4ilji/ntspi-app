<?php

namespace App\Containers\AppStructure\UI\API\Transformers;



use App\Ship\Resources\JsonResource;

class SubSectionNavigateResource extends JsonResource
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
            'pages' => PageNavigateResource::collection($this->whenLoaded('pages')),
        ];
    }
}
