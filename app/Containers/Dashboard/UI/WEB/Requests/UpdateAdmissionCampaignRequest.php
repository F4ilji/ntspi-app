<?php

namespace App\Containers\Dashboard\UI\WEB\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAdmissionCampaignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'academic_year' => ['required', 'string', 'max:20'],
            'status' => ['required', 'integer', 'in:1,2,3'],
            'info' => ['nullable', 'array'],
            'info.*.edu_name' => ['required_with:info', 'integer'],
            'info.*.total_programs' => ['required_with:info', 'integer', 'min:0'],
            'info.*.och_count' => ['required_with:info', 'integer', 'min:0'],
            'info.*.zaoch_count' => ['required_with:info', 'integer', 'min:0'],
            'info.*.budget_places' => ['required_with:info', 'integer', 'min:0'],
            'info.*.non_budget_places' => ['required_with:info', 'integer', 'min:0'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'название кампании',
            'academic_year' => 'академический год',
            'status' => 'статус кампании',
            'info' => 'информация о наборе',
        ];
    }
}
