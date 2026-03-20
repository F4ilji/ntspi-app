<?php

namespace App\Containers\Education\UI\API\Controllers;

use App\Containers\Education\Jobs\UpdateAdmissionCampaign;
use App\Containers\Education\Models\AdmissionCampaign;
use App\Containers\Education\Models\AdmissionPlan;
use App\Http\Controllers\Controller;
use App\Jobs\CreateAdmissionPlan;
use App\Jobs\CreateDirectionStudy;
use App\Jobs\CreateEducationalProgram;
use App\Services\Vicon\EducationalProgram\EducationalProgramService;
use App\Ship\Enums\Education\BudgetEducation;
use App\Ship\Enums\Education\FormEducation;

class UpdateAdmissionCampaignDataApiController extends Controller
{

    public function update(string|int $id)
    {
        $this->updateAdmissionCampaignData($id);
        return redirect()->route('index');
    }

    private function updateAdmissionCampaignData($id) : void
    {
        $ac = AdmissionCampaign::query()->find($id);
        $ap = AdmissionPlan::with('educationalProgram')->get();
        $info = [];

        $ap->map(function ($item) use (&$info) {
            $lvlEduKey = is_object($item->educationalProgram->lvl_edu)
                ? $item->educationalProgram->lvl_edu->value
                : $item->educationalProgram->lvl_edu;

            // Инициализируем поля для нового ключа, если он еще не существует
            if (!isset($info[$lvlEduKey])) {
                $info[$lvlEduKey] = [
                    'total_programs' => 0,
                    'och_count' => 0,
                    'zaoch_count' => 0,
                    'budget_places' => 0,
                    'non_budget_places' => 0
                ];
            }

            // Увеличиваем счетчик total_programs
            $info[$lvlEduKey]['total_programs'] += 1;

            // Итерируемся по contests и агрегируем данные
            // Передаем $info и $lvlEduKey по ссылке во внутреннее замыкание
            foreach ($item->contests as $contest) {
                $formEdu = $contest['form_education'];
                $formBudget = $contest['places']['form_budget'];
                $places = $contest['places']['count'];

                // Агрегируем значения
                $info[$lvlEduKey]['och_count']          += ($formEdu == 1) ? $places : 0;
                $info[$lvlEduKey]['zaoch_count']        += ($formEdu == 3) ? $places : 0;
                $info[$lvlEduKey]['budget_places']      += ($formBudget == 1) ? $places : 0;
                $info[$lvlEduKey]['non_budget_places']  += ($formBudget == 4) ? $places : 0;
            }
        });

        $result = [];

        foreach ($info as $edu_name_key => $data) {
            // Создаем новый массив, добавляя 'edu_name' как свойство
            $data['edu_name'] = $edu_name_key;

            // Добавляем преобразованный массив в результат
            $result[] = $data;
        }
        $ac->update(['info' => $result]);
        //        dispatch(new UpdateAdmissionCampaign($id));
    }
}
