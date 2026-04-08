<?php

namespace App\Containers\VikonIntegration\Actions\Updates;

use App\Containers\VikonIntegration\Tasks\CallVikonApiTask;

/**
 * Action: Check for available Vikon module updates
 *
 * Replaces old vikon_core/get_module_version.php
 */
class CheckVikonVersionAction
{
    public function __construct(
        private readonly CallVikonApiTask $callVikonApiTask,
        private readonly string $currentVersion,
    ) {}

    /**
     * Get current version and check for updates
     *
     * @param string $accessToken Valid Vikon access token
     * @return array{current_version: string, has_update: bool, latest_version: ?string}
     */
    public function run(string $accessToken): array
    {
        try {
            $response = $this->callVikonApiTask->getWithToken(
                'pull_updates/getLatestVersion',
                $accessToken
            );

            $body = $response->json();

            $latestVersion = $body['version'] ?? null;
            $hasUpdate = $latestVersion && version_compare($latestVersion, $this->currentVersion, '>');

            return [
                'current_version' => $this->currentVersion,
                'has_update' => $hasUpdate,
                'latest_version' => $latestVersion,
            ];
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Vikon version check failed', [
                'error' => $e->getMessage(),
            ]);

            return [
                'current_version' => $this->currentVersion,
                'has_update' => false,
                'latest_version' => null,
                'error' => 'Не удалось проверить наличие обновлений',
            ];
        }
    }

    /**
     * Get current version without checking for updates
     */
    public function getCurrentVersion(): string
    {
        return $this->currentVersion;
    }
}
