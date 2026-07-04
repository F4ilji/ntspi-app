<?php

namespace App\Containers\VikonIntegration\Tasks;

use Illuminate\Support\Facades\Log;

class PollPartStatusTask
{
    public function __construct(
        private readonly HttpTask $http,
        private readonly int $interval,
        private readonly int $maxAttempts,
    ) {}

    public function run(string $operationIdentity, string $accessToken): array
    {
        for ($attempt = 0; $attempt < $this->maxAttempts; $attempt++) {
            $response = $this->http->getWithToken(
                "pull_updates/getStatusPartGenerationByNewCoreJson?operation_identity={$operationIdentity}",
                $accessToken
            );

            $body = $response->json();
            $status = $body['status'] ?? 'unknown';

            Log::info('Vikon poll part status', [
                'operation' => $operationIdentity,
                'status' => $status,
                'attempt' => $attempt + 1,
            ]);

            if ($status === 'completed') {
                return ['status' => 'completed'];
            }

            if ($status === 'failed') {
                return [
                    'status' => 'failed',
                    'error' => $body['message'] ?? 'Unknown error',
                ];
            }

            if ($attempt < $this->maxAttempts - 1) {
                sleep($this->interval);
            }
        }

        return ['status' => 'timeout'];
    }
}
