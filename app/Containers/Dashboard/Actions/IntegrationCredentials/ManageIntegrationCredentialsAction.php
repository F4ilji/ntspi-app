<?php

namespace App\Containers\Dashboard\Actions\IntegrationCredentials;

use App\Containers\Dashboard\Models\IntegrationCredential;
use App\Containers\Dashboard\Tasks\IntegrationCredentials\DeleteIntegrationCredentialTask;
use App\Containers\Dashboard\Tasks\IntegrationCredentials\GetIntegrationCredentialTask;
use App\Containers\Dashboard\Tasks\IntegrationCredentials\ListIntegrationCredentialsTask;
use App\Containers\Dashboard\Tasks\IntegrationCredentials\SaveIntegrationCredentialTask;
use Illuminate\Database\Eloquent\Collection;

class ManageIntegrationCredentialsAction
{
    public function __construct(
        private readonly ListIntegrationCredentialsTask $listTask,
        private readonly SaveIntegrationCredentialTask $saveTask,
        private readonly DeleteIntegrationCredentialTask $deleteTask,
    ) {}

    public function list(): Collection
    {
        return $this->listTask->run();
    }

    public function create(array $data): IntegrationCredential
    {
        return $this->saveTask->run(
            $data['provider'],
            $data['payload'],
            $data['is_active'] ?? true,
        );
    }

    public function update(IntegrationCredential $credential, array $data): IntegrationCredential
    {
        return $this->saveTask->run(
            $data['provider'],
            $data['payload'],
            $data['is_active'] ?? true,
        );
    }

    public function delete(IntegrationCredential $credential): bool
    {
        return $this->deleteTask->run($credential);
    }
}
