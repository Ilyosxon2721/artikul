<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ReviewRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'contract_id',
        'author_id',
        'target_id',
        'role',
        'rating_overall',
        'rating_quality',
        'rating_communication',
        'rating_deadline',
        'rating_professionalism',
        'rating_clarity',
        'rating_payment_timeliness',
        'comment',
        'would_work_again',
        'is_visible',
        'published_at',
        'reply_text',
        'replied_at',
    ];

    protected $casts = [
        'rating_overall' => 'integer',
        'rating_quality' => 'integer',
        'rating_communication' => 'integer',
        'rating_deadline' => 'integer',
        'rating_professionalism' => 'integer',
        'rating_clarity' => 'integer',
        'rating_payment_timeliness' => 'integer',
        'is_visible' => 'boolean',
        'published_at' => 'datetime',
        'replied_at' => 'datetime',
        'role' => ReviewRole::class,
    ];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function target(): BelongsTo
    {
        return $this->belongsTo(User::class, 'target_id');
    }
}
