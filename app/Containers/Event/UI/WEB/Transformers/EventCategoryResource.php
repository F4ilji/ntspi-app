<?php

namespace App\Containers\Event\UI\WEB\Transformers;


use App\Ship\Resources\JsonResource;

class EventCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return parent::toArray($request);
    }
}
