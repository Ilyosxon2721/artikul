<?php

declare(strict_types=1);

namespace App\Services\Reviews;

use App\Enums\ContractStatus;
use App\Enums\ReviewRole;
use App\Models\BuyerProfile;
use App\Models\Contract;
use App\Models\Review;
use App\Models\SellerProfile;
use App\Models\User;
use App\Notifications\ReviewPublishedNotification;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ReviewService
{
    public const PUBLISH_AFTER_DAYS = 14;

    /**
     * Create a review (initially hidden) for a completed contract.
     */
    public function leave(Contract $contract, User $author, array $payload): Review
    {
        if ($contract->status !== ContractStatus::Completed) {
            throw new RuntimeException('Reviews can only be left on completed contracts.');
        }

        $isBuyer = $author->id === $contract->buyer_id;
        $isSeller = $author->id === $contract->seller_id;
        if (! $isBuyer && ! $isSeller) {
            throw new RuntimeException('You are not a party to this contract.');
        }

        $role = $isBuyer ? ReviewRole::Buyer : ReviewRole::Seller;
        $targetId = $isBuyer ? $contract->seller_id : $contract->buyer_id;

        if (Review::query()->where('contract_id', $contract->id)->where('author_id', $author->id)->exists()) {
            throw new RuntimeException('You have already reviewed this contract.');
        }

        return DB::transaction(function () use ($contract, $author, $payload, $role, $targetId): Review {
            $review = Review::create([
                'contract_id' => $contract->id,
                'author_id' => $author->id,
                'target_id' => $targetId,
                'role' => $role,
                'rating_overall' => $payload['rating_overall'],
                'rating_quality' => $payload['rating_quality'] ?? null,
                'rating_communication' => $payload['rating_communication'] ?? null,
                'rating_deadline' => $payload['rating_deadline'] ?? null,
                'rating_professionalism' => $payload['rating_professionalism'] ?? null,
                'rating_clarity' => $payload['rating_clarity'] ?? null,
                'rating_payment_timeliness' => $payload['rating_payment_timeliness'] ?? null,
                'comment' => $payload['comment'],
                'would_work_again' => $payload['would_work_again'] ?? null,
                'is_visible' => false,
            ]);

            $this->maybePublishPair($contract);

            return $review;
        });
    }

    /**
     * If both parties have reviewed (or 14 days have passed), publish reviews
     * and refresh aggregated stats.
     */
    public function maybePublishPair(Contract $contract): void
    {
        $reviews = Review::query()->where('contract_id', $contract->id)->get();

        if ($reviews->count() < 2 && $reviews->first()?->created_at?->lt(now()->subDays(self::PUBLISH_AFTER_DAYS)) !== true) {
            return;
        }

        DB::transaction(function () use ($reviews): void {
            foreach ($reviews as $review) {
                if (! $review->is_visible) {
                    $review->forceFill([
                        'is_visible' => true,
                        'published_at' => now(),
                    ])->save();

                    $this->refreshAggregatesFor((int) $review->target_id);

                    $review->target?->notify(new ReviewPublishedNotification($review));
                }
            }
        });
    }

    public function reply(Review $review, User $actor, string $text): void
    {
        if ($actor->id !== $review->target_id) {
            throw new RuntimeException('Only the target can reply.');
        }

        if ($review->reply_text !== null) {
            throw new RuntimeException('You can reply only once.');
        }

        $review->forceFill([
            'reply_text' => mb_substr($text, 0, 500),
            'replied_at' => now(),
        ])->save();
    }

    public function refreshAggregatesFor(int $userId): void
    {
        $sellerStats = Review::query()
            ->where('target_id', $userId)
            ->where('role', ReviewRole::Buyer)
            ->where('is_visible', true)
            ->selectRaw('AVG(rating_overall) as avg_rating, COUNT(*) as count')
            ->first();

        if ($sellerStats !== null) {
            SellerProfile::query()->where('user_id', $userId)->update([
                'rating_avg' => round((float) $sellerStats->avg_rating, 2),
                'reviews_count' => (int) $sellerStats->count,
            ]);
        }

        $buyerStats = Review::query()
            ->where('target_id', $userId)
            ->where('role', ReviewRole::Seller)
            ->where('is_visible', true)
            ->selectRaw('AVG(rating_overall) as avg_rating, COUNT(*) as count')
            ->first();

        if ($buyerStats !== null) {
            BuyerProfile::query()->where('user_id', $userId)->update([
                'rating_avg' => round((float) $buyerStats->avg_rating, 2),
                'reviews_count' => (int) $buyerStats->count,
            ]);
        }
    }
}
