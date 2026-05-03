<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\VerificationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Verification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'id_document_path',
        'id_selfie_path',
        'marketplace_screenshots',
        'recommendation_path',
        'passport_number_hash',
        'reviewed_by',
        'reviewed_at',
        'admin_comment',
        'rejection_reason',
    ];

    protected $hidden = [
        'passport_number_hash',
    ];

    protected $casts = [
        'marketplace_screenshots' => 'array',
        'reviewed_at' => 'datetime',
        'status' => VerificationStatus::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
