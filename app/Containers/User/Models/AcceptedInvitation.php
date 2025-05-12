<?php

namespace App\Containers\User\Models;

use App\Ship\Models\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcceptedInvitation extends Model
{
    use HasFactory;

    protected $fillable = ['sender_id', 'receiver_id', 'post_limit'];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
