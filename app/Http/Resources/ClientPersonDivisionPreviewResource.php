<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientPersonDivisionPreviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'academicTitle' => $this->userDetail->academicTitle,
            'contactPhone' => $this->userDetail->contactPhone,
            'contactEmail' => $this->userDetail->contactEmail,
            'photo' => $this->userDetail->photo,
            'administrativePosition' => $this->pivot->administrativePosition,
            'sort' => $this->pivot->sort,
            'details' => $this->userDetail,
        ];
    }
}