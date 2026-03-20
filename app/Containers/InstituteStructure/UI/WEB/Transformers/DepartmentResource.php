<?php

namespace App\Containers\InstituteStructure\UI\WEB\Transformers;


use App\Containers\User\UI\WEB\Transformers\PersonDepartmentPreviewResource;
use App\Containers\User\UI\WEB\Transformers\PersonDepartmentTeachPreviewResource;
use App\Ship\Resources\JsonResource;

class DepartmentResource extends JsonResource
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
            'faculty' => $this->faculty,
            'slug' => $this->slug,
            'workers' => PersonDepartmentPreviewResource::collection($this->workers),
            'teachers' => PersonDepartmentTeachPreviewResource::collection($this->teachers),
        ];
    }
}
