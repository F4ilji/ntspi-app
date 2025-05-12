<?php

namespace App\Containers\Widget\UI\API\Transformers;


use App\Ship\Resources\JsonResource;

class FormResource extends JsonResource
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
