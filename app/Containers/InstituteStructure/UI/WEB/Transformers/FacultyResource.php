<?php

namespace App\Containers\InstituteStructure\UI\WEB\Transformers;


use App\Containers\User\UI\WEB\Transformers\PersonFacultyPreviewResource;
use App\Ship\Resources\JsonResource;

class FacultyResource extends JsonResource
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
            'content' => $this->content,
            'shortTitle' => $this->abbreviation,
            'slug' => $this->slug,
            'workers' => PersonFacultyPreviewResource::collection($this->workers),
            'departments' => DepartmentPreviewResource::collection($this->departments)
        ];
    }
}
