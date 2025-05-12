<?php

namespace App\Containers\Education\UI\WEB\Transformers;

use App\Ship\Enums\Education\FormEducation;
use App\Ship\Resources\JsonResource;

class EducationalProgramResource extends JsonResource
{

    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'about_program' => $this->about_program,
            'program_features' => $this->program_features,
            'inner_code' => $this->inner_code,
            'lvl_edu' => $this->lvl_edu->getLabel(),
            'status' => $this->status,
//            'lang_stud' => $this->lang_stud,
            'learning_forms' => $this->transformLearningForms($this->learning_forms),
            'directionStudy' => $this->directionStudy,
            'admissionPlans' => $this->admission_plans,
        ];
    }

    private function transformLearningForms(array $learningForms): array
    {
        return array_map(function ($form) {
            $formId = $form['form_id'];
            $formEnum = FormEducation::from($formId); // Получаем enum по form_id

            return [
                'form_edu' => $formEnum->getLabel(), // Значение из enum
                'period_data' => $form['period_data'],
            ];
        }, $learningForms);
    }
}
