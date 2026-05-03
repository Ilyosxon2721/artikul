<?php

declare(strict_types=1);

namespace App\Livewire\Landing;

use App\Enums\TaskStatus;
use App\Models\Category;
use App\Models\Marketplace;
use App\Models\SellerProfile;
use App\Models\Task;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.public')]
class Home extends Component
{
    public function render(): View
    {
        return view('livewire.landing.home', [
            'marketplaces' => Marketplace::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'categories' => Category::query()->whereNull('parent_id')->where('is_active', true)->orderBy('sort_order')->limit(6)->get(),
            'topSellers' => SellerProfile::query()
                ->with('user')
                ->where('is_searchable', true)
                ->orderByDesc('is_verified')
                ->orderByDesc('rating_avg')
                ->orderByDesc('total_contracts_completed')
                ->limit(5)
                ->get(),
            'freshTasks' => Task::query()
                ->with('buyer')
                ->where('status', TaskStatus::Published)
                ->latest('published_at')
                ->limit(8)
                ->get(),
            'stats' => [
                'sellers' => SellerProfile::query()->count(),
                'marketplaces' => Marketplace::query()->where('is_active', true)->count(),
                'tasks' => Task::query()->where('status', TaskStatus::Published)->count(),
            ],
        ]);
    }
}
