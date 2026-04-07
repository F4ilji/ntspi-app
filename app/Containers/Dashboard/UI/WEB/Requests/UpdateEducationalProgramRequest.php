<?php

namespace App\Containers\Dashboard\UI\WEB\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEducationalProgramRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $programId = $this->route('educationalProgram')?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'lvl_edu' => ['required', 'integer'],
            'status' => ['required', 'integer', 'in:1,2,3,4,5,6'],
            'lang_stud' => ['required', 'string', 'max:255'],
            'direction_study_id' => ['nullable', 'exists:direction_studies,id'],
            'about_program' => ['nullable', 'array'],
            'program_features' => ['nullable', 'array'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'название программы',
            'lvl_edu' => 'уровень образования',
            'status' => 'статус программы',
            'lang_stud' => 'язык обучения',
            'direction_study_id' => 'направление подготовки',
            'about_program' => 'описание программы',
            'program_features' => 'особенности программы',
        ];
    }
}
