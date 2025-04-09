<?php

namespace App\Services\Vicon\DirectionStudy;

use App\Jobs\CreateDirectionStudy;
use App\Jobs\CreateEducationalProgram;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AdmissionPlanService
{
    public function getLevelEducationCodes(object $campaign) : array
    {
        return array_map(function ($item) {
            return (int)$item->campaign_levels_code;
        }, $campaign->groups);
    }

    public function findActiveCampaign(array $campaigns): object|null
    {
        return array_reduce($campaigns, function($carry, $item) {
            return $carry ?? ($item->status == 1 ? $item : null);
        });
    }

    public function getCampaigns(): array
    {
        try {
            $response = $this->callAPI(
                "https://db-nica.ru/api/v1/campaigns",
                env('VICON_TOKEN')
            );
            if (!is_array($response)) {
                Log::warning('Unexpected response type in getCampaigns', [
                    'type' => gettype($response),
                    'response' => $response
                ]);
            }

            return $response;
        } catch (\Exception $e) {
            Log::error('API call failed in getCampaigns', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return []; // или вернуть пустой объект (object)[]
        }
    }

    public function getAdmissionPlans(int $campaign_levels_code): object|array
    {
        try {
            $response = $this->callAPI(
                "https://db-nica.ru/api/v1/planPriema/$campaign_levels_code",
                env('VICON_TOKEN')
            );

            if (!is_object($response)) {
                Log::warning('Unexpected response type in getAdmissionPlans', [
                    'expected' => 'object',
                    'actual' => gettype($response),
                    'campaign_levels_code' => $campaign_levels_code,
                    'response' => $response
                ]);

                return [];
            }

            return $response;
        } catch (\Exception $e) {
            Log::error('API call failed in getAdmissionPlans', [
                'error' => $e->getMessage(),
                'campaign_levels_code' => $campaign_levels_code,
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    public function filterEmptyNaprOrProg(array $array): array
    {
        return array_values(array_filter($array, function ($item) {
            return !empty($item->napr_or_prog);
        }));
    }

    public function transformData(array $data) : array
    {
        return array_map(function ($item) {
            $array = [];
            $array['id'] = $item->napr_or_prog[0]->inner_code;
            $array['plan'] = $item->groups_condition[0];
            return $array;
        }, $data);
    }


    public function convertToSaveExamData(array $data) : array
    {
        return array_map(function ($item) {
            $array = [];
            $array['title'] = $item->exam_name;
            $array['priority'] = $item->priority;
            $array['types'] = array_map(function ($i) {
                return [
                    'type' => $i->type,
                    'min_ball' => $i->min_ball,
                ];
            }, $item->types);
            return $array;
        }, $data);
    }

    public function convertToSaveContestData(array $data) : array
    {
        return array_map(function ($item) {
            $array = [];
            $array['form_education'] = $item->form_obuch;
            $array['places'] = [
                'form_budget' => $item->source,
                'count' => $item->count_places
            ];
            return $array;

        }, $data);
    }

    private function callAPI(string $endpoint, string $token = null): object|array
    {
        try {
            $response = Http::withToken($token)->get($endpoint);
            $data = $response->object();

            if (isset($data->message)) {
                throw new \Exception($data->message);
            }

            return $data;
        } catch (\Exception $e) {
            Log::error('Ошибка при вызове API: ' . $e->getMessage());
            throw $e; // Перебрасываем исключение
        }
    }

}