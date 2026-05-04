<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Enums\ContractStatus;
use App\Enums\TaskStatus;
use App\Models\Contract;
use App\Models\User;
use RuntimeException;

class ContractService
{
    public function submit(Contract $contract, User $actor): Contract
    {
        $this->ensure($actor->id === $contract->seller_id, 'Only the seller can submit work.');
        $this->ensure(in_array($contract->status, [ContractStatus::InProgress, ContractStatus::Revision], true), 'Cannot submit from current status.');

        $contract->forceFill([
            'status' => ContractStatus::Submitted,
            'submitted_at' => now(),
        ])->save();

        return $contract;
    }

    public function startReview(Contract $contract, User $actor): Contract
    {
        $this->ensure($actor->id === $contract->buyer_id, 'Only the buyer can start review.');
        $this->ensure($contract->status === ContractStatus::Submitted, 'Contract is not awaiting review.');

        $contract->forceFill(['status' => ContractStatus::InReview])->save();

        return $contract;
    }

    public function approve(Contract $contract, User $actor): Contract
    {
        $this->ensure($actor->id === $contract->buyer_id, 'Only the buyer can approve work.');
        $this->ensure(in_array($contract->status, [ContractStatus::Submitted, ContractStatus::InReview], true), 'Cannot approve from current status.');

        $contract->forceFill([
            'status' => ContractStatus::Completed,
            'completed_at' => now(),
        ])->save();

        $task = $contract->task;
        if ($task !== null) {
            $task->forceFill(['status' => TaskStatus::Completed])->save();
        }

        return $contract;
    }

    public function requestRevision(Contract $contract, User $actor): Contract
    {
        $this->ensure($actor->id === $contract->buyer_id, 'Only the buyer can request revisions.');
        $this->ensure(in_array($contract->status, [ContractStatus::Submitted, ContractStatus::InReview], true), 'Cannot request revision from current status.');

        if ($contract->revisions_used >= $contract->revisions_limit) {
            throw new RuntimeException('Revision limit reached.');
        }

        $contract->forceFill([
            'status' => ContractStatus::Revision,
            'revisions_used' => $contract->revisions_used + 1,
        ])->save();

        return $contract;
    }

    public function cancel(Contract $contract, User $actor): Contract
    {
        $this->ensure(in_array($actor->id, [$contract->buyer_id, $contract->seller_id], true), 'Only contract parties can cancel.');
        $this->ensure(! in_array($contract->status, [ContractStatus::Completed, ContractStatus::Cancelled], true), 'Already finalised.');

        $contract->forceFill([
            'status' => ContractStatus::Cancelled,
            'cancelled_at' => now(),
        ])->save();

        return $contract;
    }

    private function ensure(bool $condition, string $message): void
    {
        if (! $condition) {
            throw new RuntimeException($message);
        }
    }
}
