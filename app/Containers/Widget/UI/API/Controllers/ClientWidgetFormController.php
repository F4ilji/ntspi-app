<?php

namespace App\Containers\Widget\UI\API\Controllers;

use App\Containers\Widget\Actions\SubmitWidgetFormAction;
use App\Containers\Widget\Enums\CustomFormStatus;
use App\Containers\Widget\Models\CustomForm;
use App\Containers\Widget\UI\API\Transformers\FormResource;
use App\Ship\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ClientWidgetFormController extends Controller
{
    public function __construct(
        private readonly SubmitWidgetFormAction $submitWidgetFormAction,
    ) {}

    public function single(string $id)
    {
        return new FormResource(CustomForm::query()->where('status', CustomFormStatus::PUBLISHED)->where('form_id', $id)->firstOrFail());
    }

    public function submit(int $id, Request $request)
    {
        try {
            $result = $this->submitWidgetFormAction->run($id, $request->all());
        } catch (ValidationException $exception) {
            return response()->json($exception->errors(), 422);
        }

        return response()->json($result);
    }
}
