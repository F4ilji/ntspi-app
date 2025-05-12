<?php

namespace App\Containers\User\UI\WEB\Transformers;

use App\Ship\Resources\JsonResource;


class PersonDetailResource extends JsonResource
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
