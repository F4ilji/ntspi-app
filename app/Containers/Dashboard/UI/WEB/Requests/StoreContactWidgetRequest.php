<?php

namespace App\Containers\Dashboard\UI\WEB\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreContactWidgetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('contact_widgets', 'slug')],
            'is_active' => ['boolean'],
            'content' => ['required', 'array', 'min:1'],
            'content.*.title' => ['required', 'string', 'max:255'],
            'content.*.items' => ['required', 'array', 'min:1'],
            'content.*.items.*.header' => ['required', 'string', 'max:255'],
            'content.*.items.*.details' => ['nullable', 'array'],
            'content.*.items.*.details.*.content' => ['required', 'string'],
            'content.*.items.*.details.*.url' => ['nullable', 'url'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Название ресурса обязательно для заполнения',
            'title.max' => 'Название не должно превышать 255 символов',
            'slug.required' => 'URL-адрес обязателен',
            'slug.unique' => 'Такой URL-адрес уже используется',
            'slug.max' => 'URL-адрес не должен превышать 255 символов',
            'content.required' => 'Содержание обязательно для заполнения',
            'content.*.title.required' => 'Заголовок столбца обязателен',
            'content.*.items.required' => 'Добавьте хотя бы один контактный блок',
            'content.*.items.*.header.required' => 'Заголовок контакта обязателен',
            'content.*.items.*.details.*.content.required' => 'Значение контакта обязательно',
            'content.*.items.*.details.*.url.url' => 'Укажите корректный URL',
        ];
    }
}
