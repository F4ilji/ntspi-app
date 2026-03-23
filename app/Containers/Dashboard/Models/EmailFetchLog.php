<?php

namespace App\Containers\Dashboard\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Модель лога получения Email
 * 
 * @property int $id
 * @property string $message_id
 * @property string $from_email
 * @property string|null $from_name
 * @property string $subject
 * @property \Carbon\Carbon $email_date
 * @property string $status (success, failed, skipped)
 * @property int|null $post_id
 * @property string|null $error_message
 * @property int $attachments_count
 * @property int $total_size
 * @property string|null $processed_from
 * @property array|null $metadata
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class EmailFetchLog extends Model
{
    use HasFactory;

    protected $guarded = false;

    protected $casts = [
        'email_date' => 'datetime',
        'metadata' => 'array',
        'total_size' => 'integer',
        'attachments_count' => 'integer',
    ];

    /**
     * Связь с постом
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(\App\Containers\Article\Models\Post::class);
    }

    /**
     * Scope: успешные
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    /**
     * Scope: неудачные
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope: пропущенные
     */
    public function scopeSkipped($query)
    {
        return $query->where('status', 'skipped');
    }

    /**
     * Scope: от конкретного отправителя
     */
    public function scopeFromSender($query, string $email)
    {
        return $query->where('from_email', $email);
    }

    /**
     * Создать лог успешной обработки
     */
    public static function logSuccess(
        string $messageId,
        string $fromEmail,
        string $subject,
        \DateTime $emailDate,
        int $postId,
        int $attachmentsCount,
        int $totalSize,
        array $metadata = []
    ): self {
        return static::create([
            'message_id' => $messageId,
            'from_email' => $fromEmail,
            'from_name' => null,
            'subject' => $subject,
            'email_date' => $emailDate,
            'status' => 'success',
            'post_id' => $postId,
            'error_message' => null,
            'attachments_count' => $attachmentsCount,
            'total_size' => $totalSize,
            'processed_from' => request()->ip() ?? null,
            'metadata' => $metadata,
        ]);
    }

    /**
     * Создать лог ошибки
     */
    public static function logFailed(
        string $messageId,
        string $fromEmail,
        string $subject,
        \DateTime $emailDate,
        string $errorMessage,
        int $attachmentsCount = 0,
        array $metadata = []
    ): self {
        return static::create([
            'message_id' => $messageId,
            'from_email' => $fromEmail,
            'from_name' => null,
            'subject' => $subject,
            'email_date' => $emailDate,
            'status' => 'failed',
            'post_id' => null,
            'error_message' => $errorMessage,
            'attachments_count' => $attachmentsCount,
            'total_size' => 0,
            'processed_from' => request()->ip() ?? null,
            'metadata' => $metadata,
        ]);
    }

    /**
     * Создать лог пропущенного письма
     */
    public static function logSkipped(
        string $messageId,
        string $fromEmail,
        string $subject,
        \DateTime $emailDate,
        string $reason = 'Not from allowed sender',
        array $metadata = []
    ): self {
        return static::create([
            'message_id' => $messageId,
            'from_email' => $fromEmail,
            'from_name' => null,
            'subject' => $subject,
            'email_date' => $emailDate,
            'status' => 'skipped',
            'post_id' => null,
            'error_message' => $reason,
            'attachments_count' => 0,
            'total_size' => 0,
            'processed_from' => request()->ip() ?? null,
            'metadata' => $metadata,
        ]);
    }
}
