<?php

namespace App\Jobs;

use App\Containers\Education\Models\AdmissionCampaign;
use App\Containers\Education\Models\EducationalProgram;
use App\Services\Vicon\DirectionStudy\AdmissionPlanService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CreateAdmissionPlan implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */

    private AdmissionPlanService $admissionPlanService;

    public function __construct()
    {
        $this->admissionPlanService = app(AdmissionPlanService::class);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $items = $this->admissionPlanService->getCampaigns();

            if (empty($items)) {
                Log::warning('Empty campaigns list received');
                return; // или обработка пустого случая
            }

            $campaign = $this->admissionPlanService->findActiveCampaign($items);

            if (empty($campaign)) {
                Log::warning('No active campaign found in the list');
                return;
            }

            $levels_codes = $this->admissionPlanService->getLevelEducationCodes($campaign);

            if (empty($levels_codes)) {
                Log::warning('No education level codes found for campaign');
                return;
            }

            foreach ($levels_codes as $code) {
                $admissionPlans = $this->admissionPlanService->getAdmissionPlans($code);

                // Проверка на пустой результат или отсутствие нужного свойства
                if (empty($admissionPlans) || !isset($admissionPlans->competitons_groups)) {
                    Log::warning("Empty admission plans or missing property for code: {$code}", [
                        'admissionPlans' => $admissionPlans
                    ]);
                    continue;
                }

                $filteredPlans = $this->admissionPlanService->filterEmptyNaprOrProg($admissionPlans->competitons_groups);

                if (empty($filteredPlans)) {
                    Log::info("No valid plans after filtering for code: {$code}");
                    continue;
                }

                $items = $this->admissionPlanService->transformData($filteredPlans);

                if (empty($items)) {
                    Log::info("No items after transformation for code: {$code}");
                    continue;
                }

                foreach ($items as $item) {
                    // Проверка структуры $item
                    if (!isset($item['id'], $item['plan']->competitions, $item['plan']->exams)) {
                        Log::warning("Invalid item structure", ['item' => $item]);
                        continue;
                    }

                    $eduPrograms = EducationalProgram::where('inner_code', $item['id'])->get();

                    if ($eduPrograms->isEmpty()) {
                        Log::info("No educational programs found for inner_code: {$item['id']}");
                        continue;
                    }

                    try {
                        $contestData = $this->admissionPlanService->convertToSaveContestData($item['plan']->competitions);
                        $examData = $this->admissionPlanService->convertToSaveExamData($item['plan']->exams);

                        foreach ($eduPrograms as $eduProgram) {
                            $eduProgram->admission_plans()->create([
                                'admission_campaigns_id' => $this->getActiveCampaign()->id,
                                'exams' => $examData,
                                'contests' => $contestData,
                            ]);
                        }
                    } catch (\Exception $e) {
                        Log::error("Error processing admission plan for item: {$item['id']}", [
                            'error' => $e->getMessage(),
                            'item' => $item
                        ]);
                        continue;
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Error in admission plans processing', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }


    private function getActiveCampaign()
    {
        $activeCampaign = AdmissionCampaign::where('status', 1)->first();

        if (!$activeCampaign) {
            throw new \Exception('No active admission campaign found');
        }

        return $activeCampaign;
    }
}