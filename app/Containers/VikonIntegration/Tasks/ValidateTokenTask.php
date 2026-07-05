<?php

namespace App\Containers\VikonIntegration\Tasks;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ValidateTokenTask
{
    public function __construct(
        private readonly HttpTask $http,
        private readonly string $clientId,
    ) {}

    public function run(string $accessToken): bool
    {
        $cacheKey = 'vikon_token_' . md5($accessToken);
        $cached = Cache::get($cacheKey);

        if ($cached !== null) {
            return $cached;
        }

        try {
            $response = $this->http->post('oauth2/resource/token/introspect', [
                'client_id'    => $this->clientId,
                'access_token' => $accessToken,
            ]);

            $valid = $response->successful();
            Cache::put($cacheKey, $valid, now()->addSeconds(150));

            return $valid;
        } catch (\Throwable $e) {
            Log::channel('vikon')->warning('Vikon token validation failed', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
