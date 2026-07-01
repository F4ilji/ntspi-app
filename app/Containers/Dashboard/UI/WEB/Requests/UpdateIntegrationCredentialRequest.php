<?php

namespace App\Containers\Dashboard\UI\WEB\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateIntegrationCredentialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'provider' => ['required', 'string', 'max:255', 'unique:integration_credentials,provider,' . $this->route('credential')->id],
            'payload' => ['required', 'array'],
            'payload.*' => ['required', 'string'],
            'is_active' => ['boolean'],
        ];
    }
}
