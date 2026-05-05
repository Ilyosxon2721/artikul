<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\BudgetType;
use App\Enums\ContractStatus;
use App\Enums\Currency;
use App\Enums\ProposalStatus;
use App\Enums\TaskStatus;
use App\Enums\TaskType;
use App\Models\Category;
use App\Models\Contract;
use App\Models\Marketplace;
use App\Models\Proposal;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;

/**
 * Realistic demo content for UAT and screenshots:
 *  - 12 published tasks across categories and budgets
 *  - 8 contracts on different stages (in_progress, submitted, completed, cancelled, disputed)
 *  - Spread across the demo buyers/sellers seeded by DemoUserSeeder
 *
 * Idempotent: skipped automatically if it has already inserted demo tasks.
 */
class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        if (Task::query()->where('title', 'Дизайн 10 карточек WB · кейс LookBook')->exists()) {
            return; // already seeded
        }

        $buyers = User::query()->where('email', 'like', 'buyer%@artikul.test')->get();
        $sellers = User::query()->where('email', 'like', 'seller%@artikul.test')->get();

        if ($buyers->isEmpty() || $sellers->isEmpty()) {
            return; // demo user seeder must run first
        }

        $marketplaces = Marketplace::query()->get()->keyBy('code');
        $categories = Category::query()->whereNull('parent_id')->get()->keyBy('slug');

        $tasks = [
            ['Дизайн 10 карточек WB · кейс LookBook', BudgetType::Fixed, 1_500_000, null, TaskType::Gig, 'design'],
            ['Менеджер Ozon на постоянку, 4 ч/день', BudgetType::Range, 8_000_000, 12_000_000, TaskType::Hourly, 'management'],
            ['Запуск рекламы в WB Promotion для новинки', BudgetType::Fixed, 3_000_000, null, TaskType::Project, 'ads'],
            ['Фотосессия 30 SKU косметики', BudgetType::Range, 5_000_000, 7_500_000, TaskType::Gig, 'photo-video'],
            ['Аналитика юнит-экономики бренда одежды', BudgetType::Fixed, 4_000_000, null, TaskType::Project, 'analytics'],
            ['SEO-оптимизация 50 карточек Uzum', BudgetType::Negotiable, null, null, TaskType::Gig, 'content'],
            ['Бухгалтер по ВЭД и маркетплейсам, удалённо', BudgetType::Range, 6_000_000, 9_000_000, TaskType::Hourly, 'accounting'],
            ['Видео-обзоры 15 товаров для Reels и WB', BudgetType::Fixed, 4_500_000, null, TaskType::Project, 'photo-video'],
            ['Копирайтинг карточек: 100 SKU косметики', BudgetType::Fixed, 2_500_000, null, TaskType::Gig, 'content'],
            ['Внутренняя реклама Ozon на $200k оборота', BudgetType::Range, 10_000_000, 15_000_000, TaskType::Project, 'ads'],
            ['Срочно: пересборка инфографики 20 карточек', BudgetType::Fixed, 1_200_000, null, TaskType::Gig, 'design'],
            ['Помощник менеджера Wildberries (junior)', BudgetType::Range, 3_500_000, 5_000_000, TaskType::Hourly, 'management'],
        ];

        foreach ($tasks as $i => [$title, $budgetType, $min, $max, $type, $catSlug]) {
            $buyer = $buyers[$i % $buyers->count()];
            $category = $categories[$catSlug] ?? null;

            Task::query()->updateOrCreate(
                ['title' => $title],
                [
                    'buyer_id' => $buyer->id,
                    'type' => $type,
                    'status' => TaskStatus::Published,
                    'description' => $this->descriptionFor($title, $type),
                    'marketplace_ids' => $marketplaces->pluck('id')->take(2)->all(),
                    'category_id' => $category?->id,
                    'budget_type' => $budgetType,
                    'budget_min' => $min,
                    'budget_max' => $max,
                    'currency' => Currency::UZS,
                    'deadline_at' => now()->addDays(7 + $i),
                    'estimated_duration_days' => 7 + ($i % 14),
                    'published_at' => now()->subDays(min($i, 6)),
                ],
            );
        }

        $this->seedContracts($buyers, $sellers);
    }

    private function descriptionFor(string $title, TaskType $type): string
    {
        $intro = match ($type) {
            TaskType::Gig => 'Разовая задача с конкретным результатом.',
            TaskType::Project => 'Долгосрочный проект с этапами и приёмкой.',
            TaskType::Hourly => 'Регулярная работа с почасовой оплатой.',
        };

        return $intro." {$title}.\n\nЧто нужно: высокое качество, опыт по нашему маркетплейсу, портфолио. ".
            'Мы откликаемся быстро и платим вовремя. Прикреплено ТЗ — отвечайте с ценой и сроком.';
    }

    /**
     * @param  Collection<int, User>  $buyers
     * @param  Collection<int, User>  $sellers
     */
    private function seedContracts($buyers, $sellers): void
    {
        $stages = [
            [ContractStatus::InProgress, false],
            [ContractStatus::InProgress, false],
            [ContractStatus::Submitted, false],
            [ContractStatus::InReview, false],
            [ContractStatus::Revision, false],
            [ContractStatus::Completed, false],
            [ContractStatus::Completed, false],
            [ContractStatus::Disputed, true],
        ];

        $tasks = Task::query()->where('status', TaskStatus::Published)->take(8)->get();

        foreach ($stages as $i => [$status, $hasDispute]) {
            $task = $tasks[$i] ?? null;
            if ($task === null) {
                continue;
            }

            $seller = $sellers[$i % $sellers->count()];

            $proposal = Proposal::query()->firstOrCreate(
                ['task_id' => $task->id, 'seller_id' => $seller->id],
                [
                    'status' => ProposalStatus::Accepted,
                    'cover_letter' => 'Готов взяться за задачу — есть релевантный опыт.',
                    'proposed_amount' => $task->budget_min ?? 1_000_000,
                    'currency' => $task->currency ?? Currency::UZS,
                    'rate_type' => 'fixed',
                    'proposed_deadline_days' => 14,
                ],
            );

            Contract::query()->firstOrCreate(
                ['proposal_id' => $proposal->id],
                [
                    'task_id' => $task->id,
                    'buyer_id' => $task->buyer_id,
                    'seller_id' => $seller->id,
                    'status' => $status,
                    'contract_type' => $task->type ?? TaskType::Gig,
                    'agreed_amount' => $proposal->proposed_amount,
                    'currency' => $proposal->currency ?? Currency::UZS,
                    'started_at' => now()->subDays(10 - $i),
                    'submitted_at' => in_array($status, [ContractStatus::Submitted, ContractStatus::InReview, ContractStatus::Revision, ContractStatus::Completed], true) ? now()->subDays(3) : null,
                    'completed_at' => $status === ContractStatus::Completed ? now()->subDay() : null,
                    'has_dispute' => $hasDispute,
                    'revisions_used' => $status === ContractStatus::Revision ? 1 : 0,
                    'revisions_limit' => 3,
                ],
            );
        }
    }
}
