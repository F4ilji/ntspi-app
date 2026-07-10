<?php

namespace App\Services\Vicon;

use App\Containers\Dashboard\Tasks\IntegrationCredentials\GetIntegrationCredentialTask;

class ViconApiToken
{
    public function __construct(
        private readonly GetIntegrationCredentialTask $getCredential,
    ) {}

    public function get(): string
    {
        $credential = $this->getCredential->run('vikon_api');

        if (!$credential || empty($credential->payload['token'])) {
            throw new \RuntimeException('VIKON API token not configured. Add it in /dashboard/integration-credentials.');
        }

        return $credential->payload['token'];
    }
}
