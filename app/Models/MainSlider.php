<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MainSlider extends Model
{
    use HasFactory;

    protected $guarded = false;


    protected $casts = [
        'settings' => 'array',
        'image' => 'array',
    ];
}
