<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Portfolio extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_profile_id',
        'title',
        'description',
        'cover_url',
        'media',
        'marketplace_codes',
        'specialization_id',
        'result_metrics',
        'client_name',
        'completed_on',
        'sort_order',
        'is_visible',
    ];

    protected $casts = [
        'media' => 'array',
        'marketplace_codes' => 'array',
        'result_metrics' => 'array',
        'completed_on' => 'date',
        'sort_order' => 'integer',
        'is_visible' => 'boolean',
    ];

    public function sellerProfile(): BelongsTo
    {
        return $this->belongsTo(SellerProfile::class);
    }

    public function specialization(): BelongsTo
    {
        return $this->belongsTo(Specialization::class);
    }
}
