<?php

namespace App\Containers\Education\Models;

use App\Ship\Models\Model;
use App\Ship\Traits\HasContainerFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdmissionPlan extends Model
{
    use HasContainerFactory;

    protected static string $factory = \Database\Factories\AdmissionPlanFactory::class;

    protected $guarded = false;

    protected $casts = [
        'exams' => 'array',
        'contests' => 'array'
    ];

    public function educationalProgram(): BelongsTo
    {
        return $this->belongsTo(EducationalProgram::class, 'educational_programs_id', 'id');
    }

    public function admissionCampaign(): BelongsTo
    {
        return $this->belongsTo(AdmissionCampaign::class, 'admission_campaigns_id', 'id');
    }
}
