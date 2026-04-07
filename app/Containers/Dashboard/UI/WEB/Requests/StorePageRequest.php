<?php

namespace App\Containers\Dashboard\UI\WEB\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('pages', 'slug')],
            'sub_section_id' => ['nullable', 'exists:sub_sections,id'],
            'code' => ['required', Rule::in(['200', '404', '500'])],
            'searchable' => ['boolean'],
            'icon' => ['nullable', 'string'],
            'content' => ['nullable', 'array'],
            'settings' => ['nullable', 'array'],
            'settings.hide_page_sub_section_links' => ['nullable', 'boolean'],
            'settings.hide_page_navigate_links' => ['nullable', 'boolean'],
            'settings.hide_breadcrumbs' => ['nullable', 'boolean'],
            'settings.form.id' => ['nullable', 'string'],
            'settings.form.title' => ['nullable', 'string'],
            'settings.form.description' => ['nullable', 'string'],
            'settings.form.button' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Заголовок обязателен для заполнения',
            'title.max' => 'Заголовок не должен превышать 255 символов',
            'slug.required' => 'URL-адрес обязателен',
            'slug.unique' => 'Такой URL-адрес уже используется',
            'slug.max' => 'URL-адрес не должен превышать 255 символов',
            'sub_section_id.exists' => 'Выбранный подраздел не существует',
            'code.required' => 'Код страницы обязателен',
            'code.in' => 'Код страницы должен быть 200, 404 или 500',
        ];
    }
}
