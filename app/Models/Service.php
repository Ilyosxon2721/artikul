<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Currency;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory, Sluggable, SoftDeletes;

    protected $fillable = [
        'seller_profile_id',
        'slug',
        'title',
        'description',
        'category_id',
        'specialization_id',
        'marketplace_codes',
        'price',
        'currency',
        'delivery_days',
        'revisions_included',
        'deliverables',
        'cover_url',
        'is_active',
        'orders_count',
    ];

    protected $casts = [
        'marketplace_codes' => 'array',
        'deliverables' => 'array',
        'price' => 'decimal:2',
        'delivery_days' => 'integer',
        'revisions_included' => 'integer',
        'is_active' => 'boolean',
        'orders_count' => 'integer',
        'currency' => Currency::class,
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title',
                'maxLength' => 80,
                'unique' => true,
            ],
        ];
    }

    public function sellerProfile(): BelongsTo
    {
        return $this->belongsTo(SellerProfile::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function specialization(): BelongsTo
    {
        return $this->belongsTo(Specialization::class);
    }
}
