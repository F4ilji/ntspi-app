<?php

namespace App\Containers\Dashboard\UI\WEB\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDirectionStudyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'uuid' => ['required', 'string', 'max:255', 'unique:direction_studies,uuid'],
            'slug' => ['required', 'string', 'max:255', 'unique:direction_studies,slug'],
            'code' => ['required', 'string', 'max:50'],
            'lvl_edu' => ['required', 'integer'],
            'info' => ['nullable', 'array'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'название направления',
            'uuid' => 'UUID',
            'slug' => 'URL-идентификатор',
            'code' => 'код направления',
            'lvl_edu' => 'уровень образования',
            'info' => 'информация о направлении',
        ];
    }
}
