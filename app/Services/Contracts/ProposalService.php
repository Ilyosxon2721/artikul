<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Enums\ContractStatus;
use App\Enums\Currency;
use App\Enums\ProposalStatus;
use App\Enums\TaskStatus;
use App\Models\Contract;
use App\Models\Proposal;
use App\Models\Task;
use App\Models\User;
use App\Notifications\ContractStartedNotification;
use App\Notifications\ProposalAcceptedNotification;
use App\Notifications\ProposalRejectedNotification;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ProposalService
{
    public const SELLER_DAILY_LIMIT_UNVERIFIED = 10;

    public const TASK_PROPOSAL_LIMIT = 50;

    public function create(Task $task, User $seller, array $payload): Proposal
    {
        if ($task->buyer_id === $seller->id) {
            throw new RuntimeException('Cannot apply to your own task.');
        }

        if ($task->status !== TaskStatus::Published) {
            throw new RuntimeException('Task is not open for proposals.');
        }

        if ($task->proposals_count >= self::TASK_PROPOSAL_LIMIT) {
            throw new RuntimeException('Task has reached the proposal limit.');
        }

        if (Proposal::query()->where('task_id', $task->id)->where('seller_id', $seller->id)->exists()) {
            throw new RuntimeException('You have already applied to this task.');
        }

        $verified = (bool) optional($seller->sellerProfile)->is_verified;
        if (! $verified) {
            $today = Proposal::query()
                ->where('seller_id', $seller->id)
                ->whereDate('created_at', today())
                ->count();

            if ($today >= self::SELLER_DAILY_LIMIT_UNVERIFIED) {
                throw new RuntimeException('Daily proposal limit reached for unverified sellers.');
            }
        }

        return DB::transaction(function () use ($task, $seller, $payload): Proposal {
            $proposal = Proposal::create([
                'task_id' => $task->id,
                'seller_id' => $seller->id,
                'status' => ProposalStatus::Pending,
                'cover_letter' => $payload['cover_letter'] ?? null,
                'proposed_amount' => $payload['proposed_amount'] ?? null,
                'currency' => Currency::from($payload['currency'] ?? $task->currency?->value ?? 'UZS'),
                'rate_type' => $payload['rate_type'] ?? 'fixed',
                'proposed_deadline_days' => $payload['proposed_deadline_days'] ?? null,
                'portfolio_item_ids' => $payload['portfolio_item_ids'] ?? [],
                'via_invitation' => $payload['via_invitation'] ?? false,
                'via_telegram' => $payload['via_telegram'] ?? false,
                'responded_at' => now(),
            ]);

            $task->increment('proposals_count');

            return $proposal;
        });
    }

    public function withdraw(Proposal $proposal): void
    {
        if ($proposal->status !== ProposalStatus::Pending) {
            throw new RuntimeException('Only pending proposals can be withdrawn.');
        }

        $proposal->forceFill([
            'status' => ProposalStatus::Withdrawn,
            'withdrawn_at' => now(),
        ])->save();
    }

    public function reject(Proposal $proposal): void
    {
        if (in_array($proposal->status, [ProposalStatus::Accepted, ProposalStatus::Rejected, ProposalStatus::Withdrawn], true)) {
            return;
        }

        $proposal->forceFill([
            'status' => ProposalStatus::Rejected,
            'rejected_at' => now(),
        ])->save();

        $proposal->seller?->notify(new ProposalRejectedNotification($proposal));
    }

    public function accept(Proposal $proposal): Contract
    {
        if ($proposal->status !== ProposalStatus::Pending) {
            throw new RuntimeException('Only pending proposals can be accepted.');
        }

        return DB::transaction(function () use ($proposal): Contract {
            $task = $proposal->task;

            $contract = Contract::create([
                'task_id' => $task->id,
                'proposal_id' => $proposal->id,
                'buyer_id' => $task->buyer_id,
                'seller_id' => $proposal->seller_id,
                'status' => ContractStatus::InProgress,
                'contract_type' => $task->type,
                'agreed_amount' => $proposal->proposed_amount,
                'currency' => $proposal->currency,
                'agreed_deadline' => $task->deadline_at
                    ?? ($proposal->proposed_deadline_days
                        ? now()->addDays($proposal->proposed_deadline_days)
                        : null),
                'started_at' => now(),
                'revisions_limit' => 3,
            ]);

            $proposal->forceFill(['status' => ProposalStatus::Accepted])->save();

            // Auto-reject sibling proposals
            Proposal::query()
                ->where('task_id', $task->id)
                ->where('id', '!=', $proposal->id)
                ->where('status', ProposalStatus::Pending)
                ->each(function (Proposal $sibling): void {
                    $this->reject($sibling);
                });

            $task->forceFill(['status' => TaskStatus::InProgress])->save();

            $proposal->seller?->notify(new ProposalAcceptedNotification($proposal, $contract));
            $proposal->task?->buyer?->notify(new ContractStartedNotification($contract));

            return $contract;
        });
    }
}
