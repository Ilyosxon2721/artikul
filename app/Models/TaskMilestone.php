<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Currency;
use App\Enums\MilestoneStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskMilestone extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'contract_id',
        'order_index',
        'title',
        'description',
        'acceptance_criteria',
        'amount',
        'currency',
        'deadline_at',
        'status',
        'submitted_at',
        'approved_at',
    ];

    protected $casts = [
        'order_index' => 'integer',
        'amount' => 'decimal:2',
        'deadline_at' => 'datetime',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'status' => MilestoneStatus::class,
        'currency' => Currency::class,
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }
}
