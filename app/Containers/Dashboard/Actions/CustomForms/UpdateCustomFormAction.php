<?php

namespace App\Containers\Dashboard\Actions\CustomForms;

use App\Containers\Widget\Models\CustomForm;

class UpdateCustomFormAction
{
    public function run(CustomForm $form, array $data): CustomForm
    {
        $form->update($data);

        return $form;
    }
}
