<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    use HasFactory;

    protected $guarded = false;

    protected $casts = [
        'settings' => 'array',
        'image' => 'array',
    ];

    public function slidable()
    {
        return $this->morphTo();
    }
}
