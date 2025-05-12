<?php

namespace App\Containers\Event\UI\WEB\Transformers;

use App\Ship\Resources\JsonResource;
use Carbon\Carbon;

class EventThumbnailResource extends JsonResource
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
            'is_online' => $this->is_online,
            'address' => $this->address,
            'event_date_start' => [
                'day' => Carbon::parse($this->event_date_start)->format('d'),
                'month' => Carbon::parse($this->event_date_start)->getTranslatedMonthName('Do MMMM'),
                'time' => Carbon::parse($this->event_time_start)->format('H:i'),
            ],
        ];
    }
}
