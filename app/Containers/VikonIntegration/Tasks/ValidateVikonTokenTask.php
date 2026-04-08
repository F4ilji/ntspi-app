<?php

namespace App\Containers\VikonIntegration\Tasks;

/**
 * Task: Validate Vikon access token by checking with remote API
 */
class ValidateVikonTokenTask
{
    public function __construct(
        private readonly CallVikonApiTask $callVikonApiTask,
    ) {}

    /**
     * Check if access_token is valid
     *
     * Uses auth_domain (auth.db-nica.ru) + api/profile_applicant/check_access_token
     * as per old scripts/check_token.php
     *
     * @return bool
     */
    public function run(string $accessToken): bool
    {
        try {
            $response = $this->callVikonApiTask->getWithToken(
                'api/profile_applicant/check_access_token',
                $accessToken,
                'auth'  // <-- auth domain (auth.db-nica.ru)
            );

            return $response->successful();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Vikon token validation failed', [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
