<?php

namespace App\Containers\Analytics\Models;

use Illuminate\Database\Eloquent\Model;

class AnalyticsDailyStat extends Model
{
    protected $table = 'analytics_daily_stats';

    protected $fillable = [
        'date',
        'url',
        'views_count',
        'unique_visitors',
    ];

    protected $casts = [
        'date' => 'date',
        'views_count' => 'integer',
        'unique_visitors' => 'integer',
    ];
}
