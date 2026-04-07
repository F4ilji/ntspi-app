<?php

namespace App\Containers\Dashboard\UI\WEB\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $categoryId = $this->route('category')?->id;

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('additional_education_categories', 'slug')->ignore($categoryId)],
            'dir_addit_educat_id' => ['required', 'exists:direction_additional_educations,id'],
            'is_active' => ['boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'название категории',
            'slug' => 'URL-идентификатор',
            'dir_addit_educat_id' => 'направление ДПО',
            'is_active' => 'статус активности',
        ];
    }
}
