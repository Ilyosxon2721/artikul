<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\BuyerProfile;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin BuyerProfile
 */
class BuyerProfileResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'company_name' => $this->company_name,
            'description' => $this->description,
            'marketplace_codes' => $this->marketplace_codes ?? [],
            'skus_count' => $this->skus_count,
            'revenue_range' => $this->revenue_range,
            'default_currency' => $this->default_currency?->value,
            'rating_avg' => (float) $this->rating_avg,
            'reviews_count' => $this->reviews_count,
            'total_tasks_published' => $this->total_tasks_published,
            'total_contracts_completed' => $this->total_contracts_completed,
            'on_time_payment_rate' => (float) $this->on_time_payment_rate,
        ];
    }
}
