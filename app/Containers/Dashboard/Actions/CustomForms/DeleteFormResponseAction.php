<?php

namespace App\Containers\Dashboard\Actions\CustomForms;

use App\Containers\Widget\Models\CustomFormResponse;

class DeleteFormResponseAction
{
    public function run(CustomFormResponse $response): bool
    {
        return $response->delete();
    }
}
