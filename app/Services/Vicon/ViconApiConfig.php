<?php

namespace App\Services\Vicon;

use App\Containers\Dashboard\Tasks\IntegrationCredentials\GetIntegrationCredentialTask;

class ViconApiConfig
{
    private const FALLBACK_API_URL = 'https://db-nica.ru/api/v1';

    public function __construct(
        private readonly GetIntegrationCredentialTask $getCredential,
    ) {}

    public function token(): string
    {
        $credential = $this->getCredential->run('vikon_api');

        if (!$credential || empty($credential->payload['token'])) {
            throw new \RuntimeException('VIKON API token not configured. Add it in /dashboard/integration-credentials.');
        }

        return $credential->payload['token'];
    }

    public function apiUrl(): string
    {
        $credential = $this->getCredential->run('vikon_api');

        return $credential->payload['api_url'] ?? self::FALLBACK_API_URL;
    }
}
