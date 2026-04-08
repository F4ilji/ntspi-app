<?php

namespace App\Containers\VikonIntegration\Tasks;

/**
 * Task: Refresh Vikon access token using refresh_token
 *
 * Old vikon_core used refresh_token.php with SSL verification disabled.
 * This task uses secure Http client with proper error handling.
 */
class RefreshVikonTokenTask
{
    public function __construct(
        private readonly CallVikonApiTask $callVikonApiTask,
        private readonly string $clientId,
        private readonly string $clientSecret,
    ) {}

    /**
     * Exchange refresh_token for new access_token and refresh_token
     *
     * Uses api_domain (db-nica.ru) as per old update/refresh_access_token.php
     * NOT auth_domain (auth.db-nica.ru)
     *
     * @return array{access_token: string, refresh_token: string}
     * @throws \RuntimeException
     */
    public function run(string $refreshToken): array
    {
        $response = $this->callVikonApiTask->post('oauth2/RefreshToken', [
            'refresh_token' => $refreshToken,
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type' => 'refresh_token',
        ], [], 'api');  // <-- api domain (db-nica.ru), NOT auth domain

        $body = $response->json();

        if (!isset($body['access_token']) || !isset($body['refresh_token'])) {
            throw new \RuntimeException(
                'Invalid token refresh response: ' . ($body['message'] ?? 'Unknown error')
            );
        }

        return [
            'access_token' => $body['access_token'],
            'refresh_token' => $body['refresh_token'],
        ];
    }
}
