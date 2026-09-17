<?php

namespace App\Containers\Widget\Models;

use App\Ship\Traits\HasContainerFactory;
use Illuminate\Database\Eloquent\Model;

class CustomFormResponse extends Model
{
    use HasContainerFactory;

    protected static string $factory = \Database\Factories\CustomFormResponseFactory::class;

    protected $guarded = false;

    protected $casts = [
        'answers' => 'array'
    ];

    public function form()
    {
        return $this->belongsTo(CustomForm::class, 'custom_form_id', 'id');
    }
}
