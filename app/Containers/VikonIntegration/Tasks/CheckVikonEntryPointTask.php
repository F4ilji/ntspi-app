<?php

namespace App\Containers\VikonIntegration\Tasks;

/**
 * Task: Check if current entry point is allowed in Vikon settings
 */
class CheckVikonEntryPointTask
{
    public function __construct(
        private readonly CallVikonApiTask $callVikonApiTask,
        private readonly string $clientId,
    ) {}

    /**
     * Verify that the current URL is registered as valid entry point
     *
     * @return bool
     * @throws \RuntimeException
     */
    public function run(string $entryPoint): bool
    {
        $response = $this->callVikonApiTask->get(
            'oauth2/checkEntryPoint',
            [
                'client_id' => $this->clientId,
                'entry_point' => $entryPoint,
            ]
        );

        $body = $response->json();

        return isset($body['success']) && $body['success'] === true;
    }
}
