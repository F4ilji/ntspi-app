<?php

namespace App\Containers\InstituteStructure\UI\WEB\Transformers;

use App\Ship\Resources\JsonResource;


class DepartmentPreviewResource extends JsonResource
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
        ];
    }
}
