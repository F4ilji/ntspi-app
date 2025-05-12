<?php

namespace App\Containers\Schedule\UI\WEB\Transformers;



use App\Ship\Resources\JsonResource;

class EducationalGroupResource extends JsonResource
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
            'schedules' => ScheduleGroupResource::collection($this->schedules),
            'faculty' => $this->faculty->title,
        ];
    }
}
