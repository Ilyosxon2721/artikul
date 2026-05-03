<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimeLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'seller_id',
        'work_date',
        'minutes',
        'description',
        'is_disputed',
        'dispute_reason',
        'disputed_at',
        'is_billed',
    ];

    protected $casts = [
        'work_date' => 'date',
        'minutes' => 'integer',
        'is_disputed' => 'boolean',
        'disputed_at' => 'datetime',
        'is_billed' => 'boolean',
    ];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function getHoursAttribute(): float
    {
        return round($this->minutes / 60, 2);
    }
}
