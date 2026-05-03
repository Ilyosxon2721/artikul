<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Specialization extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'slug',
        'name_ru',
        'name_uz',
        'name_en',
        'icon',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function name(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $field = "name_{$locale}";

        return $this->{$field} ?? $this->name_ru;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function sellers(): BelongsToMany
    {
        return $this->belongsToMany(SellerProfile::class, 'seller_specializations')
            ->withPivot('is_primary')
            ->withTimestamps();
    }
}
