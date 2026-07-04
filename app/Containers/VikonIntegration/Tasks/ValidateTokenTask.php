<?php

namespace App\Containers\VikonIntegration\Tasks;

use Illuminate\Support\Facades\Log;

class ValidateTokenTask
{
    public function __construct(
        private readonly HttpTask $http,
        private readonly string $clientId,
    ) {}

    public function run(string $accessToken): bool
    {
        try {
            $response = $this->http->post('oauth2/resource/token/introspect', [
                'client_id'    => $this->clientId,
                'access_token' => $accessToken,
            ]);

            return $response->successful();
        } catch (\Throwable $e) {
            Log::warning('Vikon token validation failed', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
