<?php

namespace App\Containers\Dashboard\UI\WEB\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDivisionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('divisions')->ignore($this->division)],
            'is_active' => ['boolean'],
            'description' => ['nullable', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Название подразделения обязательно для заполнения',
            'title.max' => 'Название не должно превышать 255 символов',
            'slug.required' => 'URL-адрес обязателен для заполнения',
            'slug.unique' => 'Такой URL уже используется',
        ];
    }
}
