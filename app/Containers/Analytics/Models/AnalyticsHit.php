<?php

namespace App\Containers\Analytics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalyticsHit extends Model
{
    protected $table = 'analytics_hits';

    public $timestamps = false;

    protected $fillable = [
        'session_id',
        'user_id',
        'url',
        'referrer',
        'title',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(AnalyticsSession::class, 'session_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Containers\User\Models\User::class, 'user_id');
    }
}
