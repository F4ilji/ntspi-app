<?php

namespace App\Services\Vicon\EducationalProgram;

use App\Jobs\CreateDirectionStudy;
use App\Jobs\CreateEducationalProgram;
use App\Services\Vicon\ViconApiConfig;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EducationalProgramService
{
    public function __construct(
        private readonly ViconApiConfig $viconApiConfig,
    ) {}
    const EDU_LEVELS = [1,2,3,4,5,6];

    public function getAllProgramsUuid() : array
    {
        $programs = [];
        $programsUuid = [];
        foreach (self::EDU_LEVELS as $level) {
            $data = $this->getPrograms($level);
            $programs = array_merge($programs, $data->rows);
        }
        foreach ($programs as $program) {
            $programsUuid[] = $program->uuid;
        }
        return $programsUuid;
    }

    public function getPrograms(int $edu_level) : object
    {
        $data = $this->callAPI("{$this->viconApiConfig->apiUrl()}/programs?filter_edu_level=$edu_level&perPage=200", $this->viconApiConfig->token());
        return $data;
    }

    public function getProgram(string $uuid) : object
    {
        $data = $this->callAPI("{$this->viconApiConfig->apiUrl()}/program/$uuid", $this->viconApiConfig->token());
        return $data;
    }

    private function callAPI(string $endpoint, string $token = null): object
    {
        try {
            $response = Http::withToken($token)->get($endpoint);
            $data = $response->object();

            // Проверяем наличие сообщения об ошибке в ответе
            if (isset($data->message)) {
                throw new \Exception($data->message);
            }

            return $data;
        } catch (\Exception $e) {
            Log::channel('app')->error('API call failed', ['error' => $e->getMessage()]);
            throw $e; // Перебрасываем исключение
        }
    }

}