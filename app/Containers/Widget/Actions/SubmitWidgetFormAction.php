<?php

namespace App\Containers\Widget\Actions;

use App\Containers\Widget\Tasks\GenerateWidgetFormValidationRulesTask;
use App\Containers\Widget\Tasks\GenerateWidgetFormValidationMessagesTask;
use App\Containers\Widget\Tasks\StoreWidgetFormResponseTask;
use App\Containers\Widget\Tasks\FindWidgetFormByIdTask;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class SubmitWidgetFormAction
{
    public function __construct(
        private readonly FindWidgetFormByIdTask $findWidgetFormByIdTask,
        private readonly GenerateWidgetFormValidationRulesTask $generateWidgetFormValidationRulesTask,
        private readonly GenerateWidgetFormValidationMessagesTask $generateWidgetFormValidationMessagesTask,
        private readonly StoreWidgetFormResponseTask $storeWidgetFormResponseTask,
    ) {}

    /**
     * @throws ValidationException
     */
    public function run(int $id, array $requestData): array
    {
        $formData = $this->findWidgetFormByIdTask->run($id);

        $rules = $this->generateWidgetFormValidationRulesTask->run($formData['columns'], $formData['id']);
        $messages = $this->generateWidgetFormValidationMessagesTask->run($formData['columns']);

        $validator = Validator::make($requestData, $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $this->storeWidgetFormResponseTask->run($id, $validator->validated());

        return [
            'message' => $formData['send_message'],
            'status' => 'ok'
        ];
    }
}
