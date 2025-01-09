<?php

namespace App\Models;

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
