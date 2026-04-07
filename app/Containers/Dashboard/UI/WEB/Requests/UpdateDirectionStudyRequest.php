<?php

namespace App\Containers\Dashboard\UI\WEB\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDirectionStudyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $directionId = $this->route('directionStudy')?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'uuid' => ['required', 'string', 'max:255', Rule::unique('direction_studies', 'uuid')->ignore($directionId)],
            'slug' => ['required', 'string', 'max:255', Rule::unique('direction_studies', 'slug')->ignore($directionId)],
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
