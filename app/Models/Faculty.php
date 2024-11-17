<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{
    use HasFactory;

    protected $guarded = false;

    protected $casts = [
        'content' => 'array',
    ];

    public function seo()
    {
        return $this->morphOne(Seo::class, 'seoable');
    }

    public function departments()
    {
        return $this->hasMany(Department::class);
    }

    public function workers()
    {
        return $this->belongsToMany(User::class, 'workers_faculties')->withPivot(['position', 'sort', 'service_email', 'service_phone', 'cabinet'])->whereHas('userDetail');
    }



}
