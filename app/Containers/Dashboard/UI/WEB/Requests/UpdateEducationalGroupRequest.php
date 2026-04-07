<?php

namespace App\Containers\Dashboard\UI\WEB\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEducationalGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $groupId = $this->route('educationalGroup')->id;

        return [
            'title' => ['required', 'string', 'max:50', 'unique:educational_groups,title,' . $groupId],
            'faculty_id' => ['required', 'exists:faculties,id'],
            'education_form_id' => ['required', 'in:1,2,3'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Название группы обязательно для заполнения',
            'title.unique' => 'Группа с таким названием уже существует',
            'faculty_id.required' => 'Необходимо выбрать факультет',
            'education_form_id.required' => 'Необходимо выбрать форму обучения',
        ];
    }
}
