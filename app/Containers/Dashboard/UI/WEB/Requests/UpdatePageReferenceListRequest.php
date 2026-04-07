<?php

namespace App\Containers\Dashboard\UI\WEB\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePageReferenceListRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $listId = $this->route('pageReferenceList')?->id;

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('page_reference_lists', 'slug')->ignore($listId)],
            'is_active' => ['boolean'],
            'content' => ['required', 'array', 'min:1'],
            'content.*.title' => ['required', 'string', 'max:255'],
            'content.*.link' => ['required', 'string', 'max:255'],
            'content.*.link_text' => ['required', 'string', 'max:50'],
            'content.*.image' => ['nullable', 'string'],
            'content.*.icon' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Название обязательно',
            'slug.required' => 'Slug обязателен',
            'slug.unique' => 'Такой slug уже существует',
            'content.required' => 'Добавьте хотя бы один элемент',
            'content.min' => 'Добавьте хотя бы один элемент',
            'content.*.title.required' => 'Заголовок элемента обязателен',
            'content.*.link.required' => 'Ссылка обязательна',
            'content.*.link_text.required' => 'Текст кнопки обязателен',
        ];
    }
}
