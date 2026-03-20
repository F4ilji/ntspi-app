<?php

namespace App\Containers\Education\Models;

use App\Ship\Models\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdmissionPlan extends Model
{
    use HasFactory;

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
