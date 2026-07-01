<?php

namespace App\Containers\Dashboard\Tasks\IntegrationCredentials;

use App\Containers\Dashboard\Models\IntegrationCredential;
use Illuminate\Database\Eloquent\Collection;

class ListIntegrationCredentialsTask
{
    public function run(): Collection
    {
        return IntegrationCredential::orderByDesc('id')->get();
    }
}
