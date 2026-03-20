<?php

namespace App\Containers\Widget\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactWidget extends Model
{
    use HasFactory;

    protected $guarded = false;

    protected $casts = [
        'content' => 'array',
    ];
}
