<?php

namespace App\Containers\Search\UI\API\Transformers;

use App\Containers\Schedule\UI\WEB\Transformers\ScheduleGroupResource;
use App\Ship\Resources\JsonResource;


class EducationGroupSearchResource extends JsonResource
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
            'schedules' => ScheduleGroupResource::collection($this->whenLoaded('schedules')),
        ];
    }
}
