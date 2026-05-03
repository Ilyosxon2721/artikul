<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\DisputeStatus;
use App\Enums\ReviewRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dispute extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'opened_by',
        'opened_by_role',
        'status',
        'reason_code',
        'description',
        'evidence',
        'resolved_by',
        'resolved_at',
        'resolution_summary',
        'buyer_share_percent',
        'seller_share_percent',
        'sla_due_at',
    ];

    protected $casts = [
        'evidence' => 'array',
        'resolved_at' => 'datetime',
        'sla_due_at' => 'datetime',
        'buyer_share_percent' => 'integer',
        'seller_share_percent' => 'integer',
        'status' => DisputeStatus::class,
        'opened_by_role' => ReviewRole::class,
    ];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function openedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'opened_by');
    }

    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }
}
