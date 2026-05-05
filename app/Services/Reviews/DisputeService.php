<?php

declare(strict_types=1);

namespace App\Services\Reviews;

use App\Enums\ContractStatus;
use App\Enums\DisputeStatus;
use App\Enums\ReviewRole;
use App\Models\Contract;
use App\Models\Dispute;
use App\Models\User;
use App\Notifications\DisputeOpenedNotification;
use App\Notifications\DisputeResolvedNotification;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class DisputeService
{
    public const SLA_BUSINESS_DAYS = 5;

    public function open(Contract $contract, User $actor, array $payload): Dispute
    {
        if (! in_array($contract->status, [ContractStatus::InReview, ContractStatus::Revision, ContractStatus::Submitted, ContractStatus::InProgress], true)) {
            throw new RuntimeException('Disputes can only be opened on active contracts.');
        }

        $isBuyer = $actor->id === $contract->buyer_id;
        $isSeller = $actor->id === $contract->seller_id;
        if (! $isBuyer && ! $isSeller) {
            throw new RuntimeException('You are not a party to this contract.');
        }

        return DB::transaction(function () use ($contract, $actor, $payload, $isBuyer): Dispute {
            $dispute = Dispute::create([
                'contract_id' => $contract->id,
                'opened_by' => $actor->id,
                'opened_by_role' => $isBuyer ? ReviewRole::Buyer : ReviewRole::Seller,
                'status' => DisputeStatus::Open,
                'reason_code' => $payload['reason_code'] ?? 'other',
                'description' => $payload['description'] ?? '',
                'evidence' => $payload['evidence'] ?? [],
                'sla_due_at' => now()->addWeekdays(self::SLA_BUSINESS_DAYS),
            ]);

            $contract->forceFill([
                'status' => ContractStatus::Disputed,
                'has_dispute' => true,
            ])->save();

            $contract->buyer?->notify(new DisputeOpenedNotification($dispute));
            $contract->seller?->notify(new DisputeOpenedNotification($dispute));

            return $dispute;
        });
    }

    public function resolve(Dispute $dispute, User $admin, DisputeStatus $resolution, array $payload = []): Dispute
    {
        if (! $admin->is_admin) {
            throw new RuntimeException('Only admins can resolve disputes.');
        }

        if ($dispute->status->value !== 'open' && $dispute->status->value !== 'in_review') {
            throw new RuntimeException('This dispute is already resolved.');
        }

        $dispute->forceFill([
            'status' => $resolution,
            'resolved_by' => $admin->id,
            'resolved_at' => now(),
            'resolution_summary' => $payload['summary'] ?? null,
            'buyer_share_percent' => $payload['buyer_share_percent'] ?? null,
            'seller_share_percent' => $payload['seller_share_percent'] ?? null,
        ])->save();

        $contract = $dispute->contract;
        if ($contract !== null) {
            $contract->forceFill([
                'status' => $resolution === DisputeStatus::Cancelled ? ContractStatus::InProgress : ContractStatus::Cancelled,
                'has_dispute' => false,
            ])->save();

            $contract->buyer?->notify(new DisputeResolvedNotification($dispute));
            $contract->seller?->notify(new DisputeResolvedNotification($dispute));
        }

        return $dispute;
    }
}
