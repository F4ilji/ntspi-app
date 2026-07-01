<?php

namespace App\Containers\Dashboard\Tasks\IntegrationCredentials;

use App\Containers\Dashboard\Models\IntegrationCredential;
use App\Ship\Enums\CacheKeys;
use Illuminate\Support\Facades\Cache;

class SaveIntegrationCredentialTask
{
    public function run(string $provider, array $payload, bool $isActive = true): IntegrationCredential
    {
        $credential = IntegrationCredential::updateOrCreate(
            ['provider' => $provider],
            ['payload' => $payload, 'is_active' => $isActive]
        );

        Cache::forget(CacheKeys::INTEGRATION_CREDENTIAL_PREFIX->value . $provider);

        return $credential;
    }
}
