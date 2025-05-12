<?php

namespace App\Containers\User\UI\WEB\Transformers;

use App\Ship\Resources\JsonResource;

class PersonFacultiesWorkResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id_faculty' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'position' => $this->pivot->position,
        ];
    }
}
