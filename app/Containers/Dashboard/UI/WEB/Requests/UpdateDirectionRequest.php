<?php

namespace App\Containers\Dashboard\UI\WEB\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDirectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $directionId = $this->route('direction')?->id;

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('direction_additional_educations', 'slug')->ignore($directionId)],
            'is_active' => ['boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'название направления',
            'slug' => 'URL-идентификатор',
            'is_active' => 'статус активности',
        ];
    }
}
