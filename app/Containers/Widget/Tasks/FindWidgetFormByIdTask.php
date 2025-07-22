<?php

namespace App\Containers\Widget\Tasks;

use App\Containers\Widget\Models\CustomForm;

class FindWidgetFormByIdTask
{
    public function run(int $id): CustomForm
    {
        return CustomForm::findOrFail($id);
    }
}
