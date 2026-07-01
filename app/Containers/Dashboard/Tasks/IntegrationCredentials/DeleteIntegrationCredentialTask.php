<?php

namespace App\Containers\Dashboard\Tasks\IntegrationCredentials;

use App\Containers\Dashboard\Models\IntegrationCredential;
use App\Ship\Enums\CacheKeys;
use Illuminate\Support\Facades\Cache;

class DeleteIntegrationCredentialTask
{
    public function run(IntegrationCredential $credential): bool
    {
        $provider = $credential->provider;
        $deleted = $credential->delete();

        Cache::forget(CacheKeys::INTEGRATION_CREDENTIAL_PREFIX->value . $provider);

        return $deleted;
    }
}
