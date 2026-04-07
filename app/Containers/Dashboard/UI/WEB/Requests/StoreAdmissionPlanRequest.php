<?php

namespace App\Containers\Dashboard\UI\WEB\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAdmissionPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'educational_programs_id' => ['required', 'exists:educational_programs,id'],
            'admission_campaigns_id' => ['required', 'exists:admission_campaigns,id'],
            'exams' => ['nullable', 'array'],
            'exams.*.title' => ['required_with:exams', 'string', 'max:100'],
            'exams.*.priority' => ['required_with:exams', 'integer', 'min:0'],
            'exams.*.types' => ['nullable', 'array'],
            'exams.*.types.*.type' => ['required_with:exams.*.types', 'integer'],
            'exams.*.types.*.min_ball' => ['required_with:exams.*.types', 'integer', 'min:0', 'max:100'],
            'contests' => ['nullable', 'array'],
            'contests.*.form_education' => ['required_with:contests', 'integer'],
            'contests.*.places.form_budget' => ['required_with:contests', 'integer'],
            'contests.*.places.count' => ['required_with:contests', 'integer', 'min:0'],
        ];
    }

    public function attributes(): array
    {
        return [
            'educational_programs_id' => 'образовательная программа',
            'admission_campaigns_id' => 'приемная кампания',
            'exams' => 'вступительные испытания',
            'contests' => 'условия поступления',
        ];
    }
}
