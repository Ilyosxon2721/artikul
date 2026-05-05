<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\SellerProfile;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin SellerProfile
 */
class SellerProfileResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'headline' => $this->headline,
            'bio' => $this->bio,
            'video_intro_url' => $this->video_intro_url,
            'languages' => $this->languages ?? [],
            'hourly_rate' => $this->hourly_rate ? (float) $this->hourly_rate : null,
            'currency' => $this->currency?->value,
            'min_order_amount' => $this->min_order_amount ? (float) $this->min_order_amount : null,
            'available_hours_per_week' => $this->available_hours_per_week,
            'open_to_long_term' => (bool) $this->open_to_long_term,
            'is_verified' => (bool) $this->is_verified,
            'is_pro' => (bool) $this->is_pro,
            'rating_avg' => (float) $this->rating_avg,
            'reviews_count' => $this->reviews_count,
            'total_contracts_completed' => $this->total_contracts_completed,
            'completion_rate' => (float) $this->completion_rate,
            'on_time_delivery_rate' => (float) $this->on_time_delivery_rate,
            'specializations' => $this->whenLoaded('specializations', fn () => $this->specializations->map(fn ($s) => [
                'id' => $s->id,
                'slug' => $s->slug,
                'name' => $s->name(),
            ])),
            'marketplaces' => $this->whenLoaded('marketplaces', fn () => $this->marketplaces->map(fn ($m) => [
                'id' => $m->id,
                'code' => $m->code,
                'name' => $m->name(),
                'level' => $m->pivot->level,
            ])),
        ];
    }
}
