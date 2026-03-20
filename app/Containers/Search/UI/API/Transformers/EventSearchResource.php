<?php

namespace App\Containers\Search\UI\API\Transformers;

use App\Ship\Resources\JsonResource;


class EventSearchResource extends JsonResource
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
            'event_date_start' => $this->event_date_start,
            'event_time_start' => $this->event_time_start
        ];
    }
}
