<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FullFacultyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'shortTitle' => $this->abbreviation,
            'slug' => $this->slug,
            'workers' => ClientPersonFacultyPreviewResource::collection($this->workers),
            'departments' => DepartmentPreviewResource::collection($this->departments)
        ];
    }
}
