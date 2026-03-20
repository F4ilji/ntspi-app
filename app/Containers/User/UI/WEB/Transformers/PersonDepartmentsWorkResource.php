<?php

namespace App\Containers\User\UI\WEB\Transformers;



use App\Ship\Resources\JsonResource;

class PersonDepartmentsWorkResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id_department' => $this->id,
            'title' => $this->title,
            'faculty_title' => $this->faculty->title,
            'slug' => $this->slug,
            'faculty_slug' => $this->faculty->slug,
            'position' => $this->pivot->position,
        ];
    }
}
