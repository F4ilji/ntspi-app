<?php

namespace App\Containers\Education\Models;

use App\Ship\Models\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class AdmissionCampaign extends Model
{
    use HasFactory;

    protected $guarded = false;

    protected $casts = [
      'info' => 'array'
    ];

    public function admission_plans(): HasMany
    {
        return $this->hasMany(AdmissionPlan::class, 'admission_campaigns_id', 'id');
    }

    public function educationalPrograms(): HasManyThrough
    {
        return $this->hasManyThrough(
            EducationalProgram::class,
            AdmissionPlan::class,
            'admission_campaigns_id', // внешний ключ в AdmissionPlan
            'id',                     // внешний ключ в EducationalPrograms
            'id',                     // локальный ключ в AdmissionCampaign
            'educational_programs_id' // локальный ключ в AdmissionPlan
        );
    }
}
