<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomFormResponse extends Model
{
    use HasFactory;

    protected $guarded = false;

    protected $casts = [
        'answers' => 'array'
    ];

    public function form()
    {
        return $this->belongsTo(CustomForm::class, 'custom_form_id', 'id');
    }
}
