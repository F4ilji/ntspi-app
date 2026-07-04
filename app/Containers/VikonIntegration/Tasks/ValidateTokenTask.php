<?php

namespace App\Containers\VikonIntegration\Tasks;

use Illuminate\Support\Facades\Log;

class ValidateTokenTask
{
    public function __construct(
        private readonly HttpTask $http,
    ) {}

    public function run(string $accessToken): bool
    {
        try {
            $response = $this->http->getWithToken(
                'api/profile_applicant/check_access_token',
                $accessToken,
                'auth'
            );
            return $response->successful();
        } catch (\Throwable $e) {
            Log::warning('Vikon token validation failed', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
