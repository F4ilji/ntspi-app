<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FormResponseRequest extends FormRequest
{

    private array $rules;

    public function __construct(array $rules)
    {
        $this->rules = $rules;
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        dd(1);
        return [

        ];
    }
}
