<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\UserMode;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, HasRoles, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'username',
        'email',
        'phone',
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
        'avatar_url',
        'locale',
        'country_code',
        'city',
        'timezone',
        'current_mode',
        'referral_code',
        'referred_by',
        'telegram_chat_id',
        'is_admin',
        'is_super_admin',
        'is_banned',
        'banned_reason',
        'banned_at',
        'last_ip',
        'last_active_at',
        'password_changed_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'passport_number_hash',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'banned_at' => 'datetime',
            'last_active_at' => 'datetime',
            'password_changed_at' => 'datetime',
            'two_factor_confirmed_at' => 'datetime',
            'two_factor_recovery_codes' => 'encrypted:array',
            'two_factor_secret' => 'encrypted',
            'is_admin' => 'boolean',
            'is_super_admin' => 'boolean',
            'is_banned' => 'boolean',
            'current_mode' => UserMode::class,
            'password' => 'hashed',
        ];
    }

    public function hasTwoFactorEnabled(): bool
    {
        return $this->two_factor_confirmed_at !== null && $this->two_factor_secret !== null;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_admin && ! $this->is_banned;
    }

    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    public function buyerProfile(): HasOne
    {
        return $this->hasOne(BuyerProfile::class);
    }

    public function sellerProfile(): HasOne
    {
        return $this->hasOne(SellerProfile::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'buyer_id');
    }

    public function proposals(): HasMany
    {
        return $this->hasMany(Proposal::class, 'seller_id');
    }

    public function buyerContracts(): HasMany
    {
        return $this->hasMany(Contract::class, 'buyer_id');
    }

    public function sellerContracts(): HasMany
    {
        return $this->hasMany(Contract::class, 'seller_id');
    }

    public function timeLogs(): HasMany
    {
        return $this->hasMany(TimeLog::class, 'seller_id');
    }

    public function authoredReviews(): HasMany
    {
        return $this->hasMany(Review::class, 'author_id');
    }

    public function receivedReviews(): HasMany
    {
        return $this->hasMany(Review::class, 'target_id');
    }

    public function verifications(): HasMany
    {
        return $this->hasMany(Verification::class);
    }

    public function disputes(): HasMany
    {
        return $this->hasMany(Dispute::class, 'opened_by');
    }

    public function savedSearches(): HasMany
    {
        return $this->hasMany(SavedSearch::class);
    }

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(self::class, 'referred_by');
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(self::class, 'referred_by');
    }

    public function isSeller(): bool
    {
        return $this->current_mode === UserMode::Seller;
    }

    public function isBuyer(): bool
    {
        return $this->current_mode === UserMode::Buyer;
    }
}
