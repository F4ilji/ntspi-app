<?php

namespace App\Containers\Widget\Models;

use App\Containers\Widget\Enums\CustomFormStatus;
use App\Ship\Traits\HasContainerFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomForm extends Model
{
    use HasContainerFactory;

    protected static string $factory = \Database\Factories\CustomFormFactory::class;

    protected $guarded = false;

    protected $casts = [
        'columns' => 'array',
        'status' => CustomFormStatus::class,
        'mail_settings' => 'array',
        'settings' => 'array',
    ];

    public function responses(): HasMany
    {
        return $this->hasMany(CustomFormResponse::class, 'custom_form_id', 'id');
    }
}
