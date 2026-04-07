<?php

namespace App\Containers\Dashboard\Actions\CustomForms;

use App\Containers\Widget\Models\CustomForm;
use Illuminate\Support\Str;

class CreateCustomFormAction
{
    public function run(array $data): CustomForm
    {
        $data['form_id'] = $data['form_id'] ?? Str::slug($data['title']) . time();
        $data['status'] = $data['status'] ?? 'published';
        $data['settings'] = $data['settings'] ?? [];
        $data['mail_settings'] = $data['mail_settings'] ?? [];
        $data['columns'] = $data['columns'] ?? [];

        return CustomForm::create($data);
    }
}
