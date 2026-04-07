<?php

namespace App\Containers\Dashboard\UI\WEB\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('departments')->ignore($this->department)],
            'faculty_id' => ['required', 'exists:faculties,id'],
            'is_active' => ['boolean'],
            'content' => ['nullable', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Название кафедры обязательно для заполнения',
            'title.max' => 'Название не должно превышать 255 символов',
            'slug.required' => 'URL-адрес обязателен для заполнения',
            'slug.unique' => 'Такой URL уже используется',
            'faculty_id.required' => 'Необходимо выбрать факультет',
            'faculty_id.exists' => 'Выбранный факультет не существует',
        ];
    }
}
