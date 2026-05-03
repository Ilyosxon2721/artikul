<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ContractStatus;
use App\Enums\Currency;
use App\Enums\TaskType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'task_id',
        'proposal_id',
        'buyer_id',
        'seller_id',
        'status',
        'contract_type',
        'agreed_amount',
        'currency',
        'hourly_rate',
        'agreed_deadline',
        'payment_method',
        'started_at',
        'submitted_at',
        'completed_at',
        'cancelled_at',
        'has_dispute',
        'revisions_used',
        'revisions_limit',
    ];

    protected $casts = [
        'agreed_amount' => 'decimal:2',
        'hourly_rate' => 'decimal:2',
        'agreed_deadline' => 'datetime',
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'has_dispute' => 'boolean',
        'revisions_used' => 'integer',
        'revisions_limit' => 'integer',
        'status' => ContractStatus::class,
        'contract_type' => TaskType::class,
        'currency' => Currency::class,
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function proposal(): BelongsTo
    {
        return $this->belongsTo(Proposal::class);
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(TaskMilestone::class)->orderBy('order_index');
    }

    public function timeLogs(): HasMany
    {
        return $this->hasMany(TimeLog::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function disputes(): HasMany
    {
        return $this->hasMany(Dispute::class);
    }
}
