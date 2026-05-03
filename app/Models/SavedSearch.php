<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavedSearch extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'search_type',
        'filters',
        'query',
        'notify_via',
        'frequency',
        'is_active',
        'last_alert_at',
        'last_match_seen_at',
    ];

    protected $casts = [
        'filters' => 'array',
        'notify_via' => 'array',
        'is_active' => 'boolean',
        'last_alert_at' => 'datetime',
        'last_match_seen_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
