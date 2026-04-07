<?php

namespace App\Containers\Dashboard\UI\WEB\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDirectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:direction_additional_educations,slug'],
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
