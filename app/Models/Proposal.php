<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Currency;
use App\Enums\ProposalStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Proposal extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'seller_id',
        'status',
        'cover_letter',
        'proposed_amount',
        'currency',
        'rate_type',
        'proposed_deadline_days',
        'portfolio_item_ids',
        'via_invitation',
        'via_telegram',
        'responded_at',
        'rejected_at',
        'withdrawn_at',
    ];

    protected $casts = [
        'portfolio_item_ids' => 'array',
        'proposed_amount' => 'decimal:2',
        'proposed_deadline_days' => 'integer',
        'via_invitation' => 'boolean',
        'via_telegram' => 'boolean',
        'responded_at' => 'datetime',
        'rejected_at' => 'datetime',
        'withdrawn_at' => 'datetime',
        'status' => ProposalStatus::class,
        'currency' => Currency::class,
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function contract(): HasOne
    {
        return $this->hasOne(Contract::class);
    }
}
