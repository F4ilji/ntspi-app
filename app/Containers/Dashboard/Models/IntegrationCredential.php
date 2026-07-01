<?php

namespace App\Containers\Dashboard\Models;

use Illuminate\Database\Eloquent\Model;

class IntegrationCredential extends Model
{
    protected $guarded = false;

    protected $casts = [
        'payload' => 'encrypted:array',
        'is_active' => 'boolean',
    ];
}
