<?php

namespace App\Containers\Dashboard\UI\WEB\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreAcademicJournalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:academic_journals,slug'],
            'main_info' => ['nullable', 'array'],
            'chief_editor' => ['nullable', 'array'],
            'editors' => ['nullable', 'array'],
            'for_authors' => ['nullable', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Название журнала обязательно для заполнения',
            'title.max' => 'Название журнала не должно превышать 255 символов',
            'slug.required' => 'URL-адрес обязателен для заполнения',
            'slug.unique' => 'Журнал с таким URL-адресом уже существует',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Автоматическая генерация slug если не передан
        if (empty($this->slug) && !empty($this->title)) {
            $this->merge([
                'slug' => Str::slug($this->title),
            ]);
        }

        // Генерация search_data из main_info
        if (!empty($this->main_info)) {
            $this->merge([
                'search_data' => $this->generateSearchData($this->main_info),
            ]);
        }
    }

    private function generateSearchData(array $data): string
    {
        $parts = [];
        foreach ($data as $block) {
            $parts[] = $this->getDataFromBlocks($block);
        }
        $result = implode(' ', $parts);
        $result = preg_replace('/\s+/', ' ', $result);
        $result = trim($result);

        return strtolower($result);
    }

    private function getDataFromBlocks($block): string
    {
        $data = '';
        switch ($block['type']) {
            case 'paragraph':
                $data .= strip_tags($block['data']['content']) . ' ';
                break;
            case 'heading':
                $data .= strip_tags($block['data']['content']) . ' ';
                break;
            case 'files':
                foreach ($block['data']['file'] as $file) {
                    $data .= $file['title'] . ' ';
                }
                break;
            case 'person':
                $data .= $block['data']['name'] . ' ';
                break;
            case 'stepper':
                $data .= $block['data']['step_name'] . ' ';
                foreach ($block['data']['steps'] as $step) {
                    $data .= $step['title'] . ' ';
                    $data .= strip_tags($step['content']) . ' ';
                }
                break;
            case 'tabs':
                foreach ($block['data']['tab'] as $item) {
                    foreach ($item['content'] as $blockItem) {
                        $data .= $this->getDataFromBlocks($blockItem);
                    }
                }
                break;
        }
        return $data;
    }
}
