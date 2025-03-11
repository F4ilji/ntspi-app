<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class MainSlider extends Model
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
