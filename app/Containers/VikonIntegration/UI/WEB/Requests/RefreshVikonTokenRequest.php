<?php

namespace App\Containers\VikonIntegration\UI\WEB\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RefreshVikonTokenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'refresh_token' => ['required', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'refresh_token.required' => 'Refresh token обязателен.',
        ];
    }
}
