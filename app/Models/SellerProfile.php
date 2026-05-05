<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Currency;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class SellerProfile extends Model
{
    use HasFactory, Searchable, SoftDeletes;

    protected $fillable = [
        'user_id',
        'headline',
        'bio',
        'video_intro_url',
        'languages',
        'hourly_rate',
        'currency',
        'min_order_amount',
        'available_hours_per_week',
        'open_to_long_term',
        'is_verified',
        'verified_at',
        'total_earnings',
        'earnings_currency',
        'total_contracts_completed',
        'completion_rate',
        'response_time_minutes',
        'on_time_delivery_rate',
        'rating_avg',
        'reviews_count',
        'is_pro',
        'is_searchable',
        'profile_views',
    ];

    protected $casts = [
        'languages' => 'array',
        'hourly_rate' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'available_hours_per_week' => 'integer',
        'open_to_long_term' => 'boolean',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'total_earnings' => 'decimal:2',
        'total_contracts_completed' => 'integer',
        'completion_rate' => 'decimal:2',
        'response_time_minutes' => 'integer',
        'on_time_delivery_rate' => 'decimal:2',
        'rating_avg' => 'decimal:2',
        'reviews_count' => 'integer',
        'is_pro' => 'boolean',
        'is_searchable' => 'boolean',
        'profile_views' => 'integer',
        'currency' => Currency::class,
        'earnings_currency' => Currency::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function marketplaces(): BelongsToMany
    {
        return $this->belongsToMany(Marketplace::class, 'seller_marketplaces')
            ->withPivot(['level', 'experience_months'])
            ->withTimestamps();
    }

    public function specializations(): BelongsToMany
    {
        return $this->belongsToMany(Specialization::class, 'seller_specializations')
            ->withPivot('is_primary')
            ->withTimestamps();
    }

    public function portfolios(): HasMany
    {
        return $this->hasMany(Portfolio::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function searchableAs(): string
    {
        return 'sellers';
    }

    public function shouldBeSearchable(): bool
    {
        return (bool) $this->is_searchable && ! $this->trashed();
    }

    public function toSearchableArray(): array
    {
        $this->loadMissing(['user', 'specializations', 'marketplaces']);

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'name' => $this->user?->name,
            'username' => $this->user?->username,
            'headline' => $this->headline,
            'bio' => (string) $this->bio,
            'specializations' => $this->specializations->pluck('name_ru')->all(),
            'marketplace_codes' => $this->marketplaces->pluck('code')->all(),
            'country_code' => $this->user?->country_code,
            'is_verified' => (bool) $this->is_verified,
            'is_pro' => (bool) $this->is_pro,
            'hourly_rate' => $this->hourly_rate !== null ? (float) $this->hourly_rate : null,
            'languages' => $this->languages ?? [],
            'rating_avg' => (float) $this->rating_avg,
            'reviews_count' => (int) $this->reviews_count,
            'total_contracts_completed' => (int) $this->total_contracts_completed,
            'created_at' => $this->created_at?->getTimestamp(),
        ];
    }
}
