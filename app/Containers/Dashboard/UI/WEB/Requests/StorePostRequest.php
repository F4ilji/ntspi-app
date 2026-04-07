<?php

namespace App\Containers\Dashboard\UI\WEB\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('posts', 'slug')->ignore($this->route('post'))],
            'status' => ['required', 'integer', Rule::in([0, 1, 2, 3])], // PostStatus enum values
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'tags' => ['nullable', 'array'],
            'authors' => ['nullable', 'array'],
            'content' => ['required', 'array'],
            'preview' => ['nullable', 'string'],
            'images' => ['nullable', 'array'],
            'publish_setting' => ['nullable', 'array'],
            'publish_setting.publish_after' => ['nullable', 'boolean'],
            'publish_setting.publish_at' => ['nullable', 'date', 'after:now'],
            'publication' => ['nullable', 'array'],
            'publication.vk' => ['nullable', 'boolean'],
            'publication.telegram' => ['nullable', 'boolean'],
            'is_slider_enabled' => ['nullable', 'boolean'],
            'slide' => ['nullable', 'array'],
            'slide.slider_id' => ['nullable', 'integer', 'exists:sliders,id'],
            'slide.title' => ['nullable', 'string', 'max:100'],
            'slide.content' => ['nullable', 'string', 'max:255'],
            'slide.color_theme' => ['nullable', 'string'],
            'slide.image' => ['nullable', 'array'],
            'slide.image.url' => ['nullable', 'string'],
            'slide.image.shading' => ['nullable', 'string'],
            'slide.settings' => ['nullable', 'array'],
            'slide.settings.text_position' => ['nullable', 'string', 'in:left,center,right'],
            'slide.settings.link_text' => ['nullable', 'string', 'max:20'],
            'slide.end_time' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Заголовок обязателен для заполнения',
            'title.max' => 'Заголовок не должен превышать 255 символов',
            'slug.required' => 'URL-адрес обязателен',
            'slug.unique' => 'Такой URL-адрес уже используется',
            'status.required' => 'Статус публикации обязателен',
            'status.in' => 'Некорректный статус публикации',
            'category_id.exists' => 'Выбранная категория не существует',
            'content.required' => 'Содержание новости обязательно',
            'publish_setting.publish_at.after' => 'Дата публикации должна быть в будущем',
            'slide.slider_id.exists' => 'Выбранный слайдер не существует',
            'slide.title.max' => 'Заголовок слайда не должен превышать 100 символов',
            'slide.content.max' => 'Текст слайда не должен превышать 255 символов',
            'slide.settings.text_position.in' => 'Некорректная позиция текста',
            'slide.settings.link_text.max' => 'Текст кнопки не должен превышать 20 символов',
        ];
    }
}
