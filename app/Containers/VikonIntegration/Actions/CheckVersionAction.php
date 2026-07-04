<?php

namespace App\Containers\VikonIntegration\Actions;

use App\Containers\VikonIntegration\Tasks\HttpTask;
use Illuminate\Support\Facades\Log;

class CheckVersionAction
{
    public function __construct(
        private readonly HttpTask $http,
        private readonly string $currentVersion,
    ) {}

    public function run(string $accessToken): array
    {
        try {
            $response = $this->http->getWithToken('pull_updates/assist/getUpdateVersionJson', $accessToken);
            $body = $response->json();
            $latest = $body['version'] ?? null;
            return [
                'current_version' => $this->currentVersion,
                'latest_version' => $latest,
                'has_update' => $latest && version_compare($latest, $this->currentVersion, '>'),
            ];
        } catch (\Throwable $e) {
            Log::warning('Version check failed', ['error' => $e->getMessage()]);
            return [
                'current_version' => $this->currentVersion,
                'latest_version' => null,
                'has_update' => false,
                'error' => 'Не удалось проверить обновления',
            ];
        }
    }
}
