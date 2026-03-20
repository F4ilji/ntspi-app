<?php

namespace App\Containers\InstituteStructure\Models;

use App\Containers\Education\Models\EducationalProgram;
use App\Containers\User\Models\User;
use App\Ship\Models\Model;
use App\Ship\Traits\HasSeo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Department extends Model
{
    use HasFactory, HasSeo;

    protected $guarded = false;

    protected $casts = [
        'content' => 'array',
    ];

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    public function workers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'workers_departments')->withPivot(['position', 'sort', 'service_email', 'service_phone', 'cabinet']);
    }

    public function programs(): BelongsToMany
    {
        return $this->belongsToMany(EducationalProgram::class, 'program_department');
    }

    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'teachers_departments')->withPivot(['teaching_position', 'sort', 'service_email', 'service_phone', 'cabinet']);
    }
}
