<?php

namespace App\Containers\VikonIntegration\Actions\Auth;

use App\Containers\VikonIntegration\Tasks\CallVikonApiTask;

/**
 * Action: Authenticate with Vikon using authorization code
 *
 * Exchanges OAuth2 authorization code for access_token and refresh_token.
 * Replaces old vikon_core/get_access_token.php with proper error handling.
 */
class AuthenticateVikonAction
{
    public function __construct(
        private readonly CallVikonApiTask $callVikonApiTask,
        private readonly string $clientId,
        private readonly ?string $clientSecret,
    ) {}

    /**
     * Exchange authorization code for tokens
     *
     * @param string $code Authorization code from Vikon OAuth
     * @param string $redirectUri Current URL for validation
     * @return array{access_token: string, refresh_token: string}
     * @throws \RuntimeException
     */
    public function run(string $code, string $redirectUri): array
    {
        if (empty($this->clientSecret)) {
            throw new \RuntimeException(
                'VIKON_CLIENT_SECRET не настроен. Обратитесь к администратору.'
            );
        }
        $response = $this->callVikonApiTask->post('oauth2/authorize/token', [
            'code' => $code,
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri' => $redirectUri,
            'grant_type' => 'authorization_code',
        ], [], 'auth');

        $body = $response->json();

        if (!isset($body['access_token']) || !isset($body['refresh_token'])) {
            throw new \RuntimeException(
                'Authentication failed: ' . ($body['message'] ?? 'Unknown error')
            );
        }

        return [
            'access_token' => $body['access_token'],
            'refresh_token' => $body['refresh_token'],
        ];
    }
}
