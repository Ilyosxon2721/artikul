<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Proposal;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Proposal
 */
class ProposalResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'task_id' => $this->task_id,
            'seller_id' => $this->seller_id,
            'status' => $this->status?->value,
            'cover_letter' => $this->cover_letter,
            'proposed_amount' => $this->proposed_amount !== null ? (float) $this->proposed_amount : null,
            'currency' => $this->currency?->value,
            'rate_type' => $this->rate_type,
            'proposed_deadline_days' => $this->proposed_deadline_days,
            'via_invitation' => (bool) $this->via_invitation,
            'via_telegram' => (bool) $this->via_telegram,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
