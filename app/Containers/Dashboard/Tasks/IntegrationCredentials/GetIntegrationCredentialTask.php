<?php

namespace App\Containers\Dashboard\Tasks\IntegrationCredentials;

use App\Containers\Dashboard\Models\IntegrationCredential;
use App\Ship\Enums\CacheKeys;
use Illuminate\Support\Facades\Cache;

class GetIntegrationCredentialTask
{
    public function run(string $provider): ?IntegrationCredential
    {
        $cacheKey = CacheKeys::INTEGRATION_CREDENTIAL_PREFIX->value . $provider;

        return Cache::remember($cacheKey, now()->addHour(), function () use ($provider) {
            return IntegrationCredential::where('provider', $provider)
                ->where('is_active', true)
                ->first();
        });
    }
}
