<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $guarded = false;

    protected $casts = [
        'content' => 'array',
    ];

    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    public function workers()
    {
        return $this->belongsToMany(User::class, 'workers_departments')->withPivot(['position']);
    }

    public function programs()
    {
        return $this->belongsToMany(EducationalProgram::class, 'program_department');
    }

    public function teachers()
    {
        return $this->belongsToMany(User::class, 'teachers_departments')->withPivot(['teaching_position']);
    }
}
