<?php

namespace App\Containers\Dashboard\UI\WEB\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAdditionalEducationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $educationId = $this->route('additionalEducation')?->id;

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('additional_educations', 'slug')->ignore($educationId)],
            'category_id' => ['required', 'exists:additional_education_categories,id'],
            'target_group' => ['required', 'string', 'max:255'],
            'qualification' => ['nullable', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'learning_time' => ['required', 'integer', 'min:1'],
            'form_education' => ['required', 'integer', 'in:1,2,3'],
            'is_active' => ['boolean'],
            'content' => ['nullable', 'array'],
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'название программы',
            'slug' => 'URL-адрес',
            'category_id' => 'категория',
            'target_group' => 'целевая аудитория',
            'qualification' => 'выдаваемый документ',
            'price' => 'стоимость',
            'learning_time' => 'объем (часов)',
            'form_education' => 'форма обучения',
            'is_active' => 'статус активности',
            'content' => 'содержание программы',
        ];
    }
}
