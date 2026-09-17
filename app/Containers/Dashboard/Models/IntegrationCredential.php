<?php

namespace App\Containers\Dashboard\Models;

use App\Ship\Traits\HasContainerFactory;
use Illuminate\Database\Eloquent\Model;

class IntegrationCredential extends Model
{
    use HasContainerFactory;

    protected static string $factory = \Database\Factories\IntegrationCredentialFactory::class;

    protected $guarded = false;

    protected $casts = [
        'payload' => 'encrypted:array',
        'is_active' => 'boolean',
    ];
}
