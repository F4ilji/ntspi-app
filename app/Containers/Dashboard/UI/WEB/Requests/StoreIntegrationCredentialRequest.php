<?php

namespace App\Containers\Dashboard\UI\WEB\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreIntegrationCredentialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'provider' => ['required', 'string', 'max:255', 'unique:integration_credentials,provider'],
            'payload' => ['required', 'array'],
            'payload.*' => ['required', 'string'],
            'is_active' => ['boolean'],
        ];
    }
}
