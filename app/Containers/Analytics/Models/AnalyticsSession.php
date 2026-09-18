<?php

namespace App\Containers\Analytics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalyticsSession extends Model
{
    protected $table = 'analytics_sessions';

    protected $fillable = [
        'visitor_id',
        'user_id',
        'entry_page',
        'ip',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'browser',
        'os',
        'device_type',
        'started_at',
        'last_activity_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'last_activity_at' => 'datetime',
    ];

    public function hits(): HasMany
    {
        return $this->hasMany(AnalyticsHit::class, 'session_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Containers\User\Models\User::class, 'user_id');
    }

    public function firstOrCreateSession(
        string $visitorId,
        string $ip,
        int $userId = null,
        string $entryPage = null,
        array $utmParams = [],
    ): self {
        $lastSession = static::where('visitor_id', $visitorId)
            ->where('last_activity_at', '>', now()->subMinutes(30))
            ->latest('last_activity_at')
            ->first();

        if ($lastSession) {
            $lastSession->update(['last_activity_at' => now()]);
            return $lastSession;
        }

        return static::create([
            'visitor_id' => $visitorId,
            'user_id' => $userId,
            'entry_page' => $entryPage,
            'ip' => $ip,
            'utm_source' => $utmParams['utm_source'] ?? null,
            'utm_medium' => $utmParams['utm_medium'] ?? null,
            'utm_campaign' => $utmParams['utm_campaign'] ?? null,
            'started_at' => now(),
            'last_activity_at' => now(),
        ]);
    }
}
