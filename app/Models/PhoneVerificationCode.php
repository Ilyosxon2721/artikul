<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhoneVerificationCode extends Model
{
    protected $fillable = [
        'phone',
        'code_hash',
        'purpose',
        'attempts',
        'expires_at',
        'locked_until',
        'ip_address',
        'consumed_at',
    ];

    protected $casts = [
        'attempts' => 'integer',
        'expires_at' => 'datetime',
        'locked_until' => 'datetime',
        'consumed_at' => 'datetime',
    ];
}
