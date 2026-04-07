<?php

namespace App\Containers\Dashboard\Actions\CustomForms;

use App\Containers\Widget\Models\CustomForm;

class DeleteCustomFormAction
{
    public function run(CustomForm $form): bool
    {
        return $form->delete();
    }
}
