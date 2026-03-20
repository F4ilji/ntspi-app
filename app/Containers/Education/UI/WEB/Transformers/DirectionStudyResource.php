<?php

namespace App\Containers\Education\UI\WEB\Transformers;

use App\Ship\Resources\JsonResource;

class DirectionStudyResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'programs' => EducationalProgramBasicResource::collection($this->programs),
        ];
    }
}
