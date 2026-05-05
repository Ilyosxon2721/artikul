<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\BudgetType;
use App\Enums\Currency;
use App\Enums\SellerLevel;
use App\Enums\TaskStatus;
use App\Enums\TaskType;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class Task extends Model
{
    use HasFactory, Searchable, Sluggable, SoftDeletes;

    protected $fillable = [
        'buyer_id',
        'type',
        'status',
        'slug',
        'title',
        'description',
        'marketplace_ids',
        'category_id',
        'subcategory_id',
        'specialization_id',
        'volume_unit',
        'volume_amount',
        'budget_type',
        'budget_min',
        'budget_max',
        'currency',
        'deadline_at',
        'estimated_duration_days',
        'required_seller_level',
        'require_verified_seller',
        'required_languages',
        'required_country',
        'requirements',
        'invitation_only',
        'invited_seller_ids',
        'published_at',
        'closed_at',
        'views_count',
        'proposals_count',
        'invitations_count',
    ];

    protected $casts = [
        'marketplace_ids' => 'array',
        'invited_seller_ids' => 'array',
        'required_languages' => 'array',
        'requirements' => 'array',
        'budget_min' => 'decimal:2',
        'budget_max' => 'decimal:2',
        'volume_amount' => 'integer',
        'estimated_duration_days' => 'integer',
        'require_verified_seller' => 'boolean',
        'invitation_only' => 'boolean',
        'deadline_at' => 'datetime',
        'published_at' => 'datetime',
        'closed_at' => 'datetime',
        'views_count' => 'integer',
        'proposals_count' => 'integer',
        'invitations_count' => 'integer',
        'type' => TaskType::class,
        'status' => TaskStatus::class,
        'budget_type' => BudgetType::class,
        'currency' => Currency::class,
        'required_seller_level' => SellerLevel::class,
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

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'subcategory_id');
    }

    public function specialization(): BelongsTo
    {
        return $this->belongsTo(Specialization::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TaskAttachment::class);
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(TaskMilestone::class)->orderBy('order_index');
    }

    public function proposals(): HasMany
    {
        return $this->hasMany(Proposal::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function searchableAs(): string
    {
        return 'tasks';
    }

    public function shouldBeSearchable(): bool
    {
        return $this->status?->value === 'published';
    }

    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => (string) $this->description,
            'slug' => $this->slug,
            'task_type' => $this->type?->value,
            'status' => $this->status?->value,
            'category_id' => $this->category_id,
            'subcategory_id' => $this->subcategory_id,
            'marketplace_ids' => $this->marketplace_ids ?? [],
            'budget_min' => $this->budget_min !== null ? (float) $this->budget_min : null,
            'budget_max' => $this->budget_max !== null ? (float) $this->budget_max : null,
            'currency' => $this->currency?->value,
            'require_verified_seller' => (bool) $this->require_verified_seller,
            'published_at' => $this->published_at?->getTimestamp(),
            'deadline_at' => $this->deadline_at?->getTimestamp(),
        ];
    }
}
