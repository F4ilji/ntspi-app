<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seo extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description'];

    public function seoable()
    {
        return $this->morphTo();
    }
}
