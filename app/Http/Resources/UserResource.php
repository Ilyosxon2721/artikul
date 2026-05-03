<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User
 */
class UserResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'phone' => $this->phone,
            'avatar_url' => $this->avatar_url,
            'locale' => $this->locale,
            'country_code' => $this->country_code,
            'city' => $this->city,
            'timezone' => $this->timezone,
            'current_mode' => $this->current_mode?->value ?? 'buyer',
            'is_admin' => (bool) $this->is_admin,
            'email_verified_at' => $this->email_verified_at,
            'phone_verified_at' => $this->phone_verified_at,
            'referral_code' => $this->referral_code,
            'created_at' => $this->created_at,
        ];
    }
}
