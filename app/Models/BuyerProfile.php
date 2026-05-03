<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Currency;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BuyerProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'company_name',
        'description',
        'marketplace_codes',
        'skus_count',
        'revenue_range',
        'default_currency',
        'total_tasks_published',
        'total_contracts_completed',
        'total_spent',
        'total_spent_currency',
        'rating_avg',
        'reviews_count',
        'on_time_payment_rate',
    ];

    protected $casts = [
        'marketplace_codes' => 'array',
        'skus_count' => 'integer',
        'total_tasks_published' => 'integer',
        'total_contracts_completed' => 'integer',
        'total_spent' => 'decimal:2',
        'rating_avg' => 'decimal:2',
        'reviews_count' => 'integer',
        'on_time_payment_rate' => 'decimal:2',
        'default_currency' => Currency::class,
        'total_spent_currency' => Currency::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
