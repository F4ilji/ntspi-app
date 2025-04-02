<?php

namespace App\Models;

use App\Enums\CustomFormStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomForm extends Model
{
    use HasFactory;

    protected $guarded = false;

    protected $casts = [
        'columns' => 'array',
        'status' => CustomFormStatus::class,
        'mail_settings' => 'array',
        'settings' => 'array',
    ];

    public function responses()
    {
        return $this->hasMany(CustomFormResponse::class, 'custom_form_id', 'id');
    }
}
