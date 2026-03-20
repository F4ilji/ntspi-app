<?php

namespace App\Containers\Widget\Tasks;

use App\Containers\Widget\Models\CustomFormResponse;

class StoreWidgetFormResponseTask
{
    public function run(int $formId, array $validatedData): void
    {
        CustomFormResponse::create([
            'custom_form_id' => $formId,
            'answers' => $validatedData,
        ]);
    }
}
