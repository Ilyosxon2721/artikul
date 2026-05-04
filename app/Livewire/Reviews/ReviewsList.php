<?php

declare(strict_types=1);

namespace App\Livewire\Reviews;

use App\Models\Review;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class ReviewsList extends Component
{
    public int $userId;

    public string $sort = 'newest';

    public function mount(int $userId): void
    {
        $this->userId = $userId;
    }

    public function render(): View
    {
        $query = Review::query()
            ->with('author')
            ->where('target_id', $this->userId)
            ->where('is_visible', true);

        $query = match ($this->sort) {
            'rating_desc' => $query->orderByDesc('rating_overall'),
            'rating_asc' => $query->orderBy('rating_overall'),
            default => $query->latest('published_at'),
        };

        return view('livewire.reviews.reviews-list', [
            'reviews' => $query->paginate(10),
        ]);
    }
}
