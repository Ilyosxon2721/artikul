<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Contract
 */
class ContractResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'task_id' => $this->task_id,
            'buyer_id' => $this->buyer_id,
            'seller_id' => $this->seller_id,
            'status' => $this->status?->value,
            'contract_type' => $this->contract_type?->value,
            'agreed_amount' => $this->agreed_amount !== null ? (float) $this->agreed_amount : null,
            'currency' => $this->currency?->value,
            'started_at' => $this->started_at?->toIso8601String(),
            'submitted_at' => $this->submitted_at?->toIso8601String(),
            'completed_at' => $this->completed_at?->toIso8601String(),
            'cancelled_at' => $this->cancelled_at?->toIso8601String(),
            'has_dispute' => (bool) $this->has_dispute,
            'revisions_used' => $this->revisions_used,
            'revisions_limit' => $this->revisions_limit,
        ];
    }
}
