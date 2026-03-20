<?php

namespace App\Containers\User\UI\WEB\Transformers;

use App\Ship\Resources\JsonResource;


class PersonDivisionPreviewResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'academicTitle' => $this->userDetail->academicTitle,
            'contactPhone' => $this->pivot->service_phone,
            'contactEmail' => $this->pivot->service_email,
            'cabinet' => $this->pivot->cabinet,
            'photo' => $this->userDetail->photo,
            'administrativePosition' => $this->pivot->administrativePosition,
            'sort' => $this->pivot->sort,
            'details' => $this->userDetail,
        ];
    }
}
