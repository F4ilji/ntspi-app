<?php

namespace App\Containers\Dashboard\UI\WEB\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCustomFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'form_id' => ['required', 'string', 'max:255', Rule::unique('custom_forms', 'form_id')],
            'description' => ['required', 'string', 'max:2000'],
            'status' => ['required', Rule::in(['published', 'hidden'])],
            'button' => ['required', 'string', 'max:255'],
            'send_message' => ['required', 'string', 'max:1000'],
            'columns' => ['nullable', 'array'],
            'settings' => ['nullable', 'array'],
            'settings.personal_data' => ['nullable', 'boolean'],
            'settings.captcha' => ['nullable', 'boolean'],
            'settings.period' => ['nullable', 'array'],
            'mail_settings' => ['nullable', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Название формы обязательно',
            'title.max' => 'Название не должно превышать 255 символов',
            'form_id.required' => 'Уникальный ID формы обязателен',
            'form_id.unique' => 'Такой ID формы уже существует',
            'description.required' => 'Описание обязательно',
            'status.required' => 'Статус обязателен',
            'status.in' => 'Статус должен быть published или hidden',
            'button.required' => 'Текст кнопки обязателен',
            'send_message.required' => 'Сообщение после отправки обязательно',
        ];
    }
}
