<?php

declare(strict_types=1);

use App\Enums\BudgetType;
use App\Enums\ContractStatus;
use App\Enums\Currency;
use App\Enums\ProposalStatus;
use App\Enums\TaskStatus;
use App\Enums\TaskType;
use App\Models\BuyerProfile;
use App\Models\Category;
use App\Models\Contract;
use App\Models\Marketplace;
use App\Models\Review;
use App\Models\SellerProfile;
use App\Models\Task;
use App\Models\User;
use App\Services\Contracts\ContractService;
use App\Services\Contracts\ProposalService;
use App\Services\Reviews\ReviewService;

it('end-to-end: register buyer + seller, post task, apply, accept, complete, review', function () {
    // Reference data
    $wb = Marketplace::query()->firstOrCreate(['code' => 'WB'], [
        'name_ru' => 'Wildberries', 'name_uz' => 'Wildberries', 'name_en' => 'Wildberries',
        'country_code' => 'RU', 'is_active' => true, 'sort_order' => 1,
    ]);
    $design = Category::query()->firstOrCreate(['slug' => 'design'], [
        'name_ru' => 'Дизайн', 'name_uz' => 'Dizayn', 'name_en' => 'Design',
        'is_active' => true, 'sort_order' => 1,
    ]);

    // 1. Register buyer
    $buyer = User::factory()->create();
    BuyerProfile::create(['user_id' => $buyer->id]);

    // 2. Register seller (verified)
    $seller = User::factory()->create();
    SellerProfile::create(['user_id' => $seller->id, 'is_verified' => true]);

    // 3. Buyer publishes a task
    $task = Task::create([
        'buyer_id' => $buyer->id,
        'type' => TaskType::Gig,
        'status' => TaskStatus::Published,
        'title' => 'WB cards design — 10 SKUs',
        'description' => str_repeat('Need premium infographics for WB cards. ', 5),
        'marketplace_ids' => [$wb->id],
        'category_id' => $design->id,
        'budget_type' => BudgetType::Fixed,
        'budget_min' => 1500000,
        'currency' => Currency::UZS,
        'published_at' => now(),
    ]);

    // 4. Seller applies
    $proposal = app(ProposalService::class)->create($task, $seller, [
        'cover_letter' => str_repeat('I have done 200+ WB cards. ', 3),
        'proposed_amount' => 1200000,
        'currency' => 'UZS',
        'rate_type' => 'fixed',
        'proposed_deadline_days' => 7,
    ]);

    expect($proposal->status)->toBe(ProposalStatus::Pending);
    expect($task->fresh()->proposals_count)->toBe(1);

    // 5. Buyer accepts → contract starts, conversation opens
    $contract = app(ProposalService::class)->accept($proposal->fresh());
    expect($contract->status)->toBe(ContractStatus::InProgress);
    expect($task->fresh()->status)->toBe(TaskStatus::InProgress);

    // 6. Seller submits work
    app(ContractService::class)->submit($contract, $seller);
    expect($contract->fresh()->status)->toBe(ContractStatus::Submitted);

    // 7. Buyer approves → contract completed, task completed, review request fired
    app(ContractService::class)->approve($contract->fresh(), $buyer);
    expect($contract->fresh()->status)->toBe(ContractStatus::Completed);
    expect($task->fresh()->status)->toBe(TaskStatus::Completed);

    // 8. Both sides leave reviews → publish via blind-method
    $reviews = app(ReviewService::class);
    $reviews->leave($contract->fresh(), $buyer, [
        'rating_overall' => 5,
        'rating_quality' => 5,
        'rating_communication' => 5,
        'rating_deadline' => 5,
        'comment' => str_repeat('Top-tier work, would hire again. ', 3),
    ]);
    $reviews->leave($contract->fresh(), $seller, [
        'rating_overall' => 5,
        'rating_clarity' => 5,
        'rating_communication' => 5,
        'rating_payment_timeliness' => 5,
        'comment' => str_repeat('Clear brief, prompt payment. ', 3),
    ]);

    expect(Review::where('contract_id', $contract->id)->where('is_visible', true)->count())->toBe(2);
    expect((float) $seller->fresh()->sellerProfile->rating_avg)->toBe(5.0);
    expect((float) $buyer->fresh()->buyerProfile->rating_avg)->toBe(5.0);
});
