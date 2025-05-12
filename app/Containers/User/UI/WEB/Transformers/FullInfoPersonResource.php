<?php

namespace App\Containers\User\UI\WEB\Transformers;

use App\Ship\Resources\JsonResource;

class FullInfoPersonResource extends JsonResource
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
            'name' => $this->name,
            'details' => new PersonDetailResource($this->whenLoaded('userDetail')),
            'departments_work' => PersonDepartmentsWorkResource::collection($this->whenLoaded('departments_work')),
            'departments_teach' => PersonDepartmentsTeachResource::collection($this->whenLoaded('departments_teach')),
            'divisions_works' => PersonDivisionsWorkResource::collection($this->whenLoaded('divisions')),
            'faculties_works' => PersonFacultiesWorkResource::collection($this->whenLoaded('faculties'))
        ];
    }
}
