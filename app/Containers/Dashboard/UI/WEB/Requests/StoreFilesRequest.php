<?php

namespace App\Containers\Dashboard\UI\WEB\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFilesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Поле для всех файлов (множественная загрузка)
            'files' => [
                'required',
                'array',
                'min:1',
                'max:20' // Максимум 20 файлов за раз
            ],

            // Проверка каждого файла ВНУТРИ массива files
            'files.*' => [
                'required',
                'file',
                'mimes:doc,docx,pdf,xls,xlsx,jpg,jpeg,png,webp,gif,zip',
                'max:20480' // макс 20МБ на КАЖДЫЙ файл
            ],

            // Обязательно наличие хотя бы одного DOC или DOCX файла (или архива, который будет распакован)
            'files' => [
                'required',
                'array',
                function ($attribute, $value, $fail) {
                    $hasDocOrDocx = collect($value)->contains(function ($file) {
                        $ext = strtolower($file->getClientOriginalExtension());
                        return in_array($ext, ['doc', 'docx']);
                    });
                    
                    $hasArchive = collect($value)->contains(function ($file) {
                        $ext = strtolower($file->getClientOriginalExtension());
                        return in_array($ext, ['zip']);
                    });

                    // Если есть только архивы — проверяем их содержимое после распаковки
                    if (!$hasDocOrDocx && $hasArchive) {
                        // Разрешаем, проверка будет после распаковки
                        return;
                    }

                    if (!$hasDocOrDocx && !$hasArchive) {
                        $fail('Должен быть загружен хотя бы один DOC или DOCX файл для извлечения текста новости.');
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'files.required' => 'Загрузите хотя бы один файл',
            'files.array' => 'Поле должно быть массивом файлов',
            'files.min' => 'Загрузите хотя бы один файл',
            'files.max' => 'Нельзя загрузить больше 20 файлов за один раз',
            'files.*.mimes' => 'Файл имеет недопустимый формат. Разрешены: DOC, DOCX, PDF, XLS, XLSX, JPG, JPEG, PNG, WEBP, GIF',
            'files.*.max' => 'Размер файла превышает 20 МБ.',
        ];
    }
}