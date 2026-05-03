<?php

declare(strict_types=1);

namespace App\Livewire\Sellers;

use App\Models\Marketplace;
use App\Models\SellerProfile;
use App\Models\Specialization;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class SellerCatalog extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    /** @var array<int> */
    #[Url(as: 'spec')]
    public array $specializationIds = [];

    /** @var array<int> */
    #[Url(as: 'mp')]
    public array $marketplaceIds = [];

    #[Url(as: 'rate_min')]
    public ?int $rateMin = null;

    #[Url(as: 'rate_max')]
    public ?int $rateMax = null;

    #[Url(as: 'rating')]
    public ?int $minRating = null;

    #[Url(as: 'country')]
    public string $country = '';

    #[Url(as: 'verified')]
    public bool $verifiedOnly = false;

    #[Url(as: 'sort')]
    public string $sort = 'recommended';

    public function updating(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'specializationIds', 'marketplaceIds', 'rateMin', 'rateMax', 'minRating', 'country', 'verifiedOnly']);
    }

    public function render(): View
    {
        return view('livewire.sellers.seller-catalog', [
            'sellers' => $this->results(),
            'specializations' => Specialization::query()->where('is_active', true)->orderBy('sort_order')->orderBy('name_ru')->get(),
            'marketplaces' => Marketplace::query()->where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }

    /**
     * @return LengthAwarePaginator<int, SellerProfile>
     */
    private function results(): LengthAwarePaginator
    {
        $query = SellerProfile::query()
            ->with(['user', 'specializations', 'marketplaces'])
            ->where('is_searchable', true)
            ->whereHas('user', fn ($q) => $q->where('is_banned', false));

        if ($this->search !== '') {
            $term = '%'.str_replace(['%', '_'], ['\%', '\_'], $this->search).'%';
            $query->where(function ($q) use ($term): void {
                $q->where('headline', 'like', $term)
                    ->orWhere('bio', 'like', $term)
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', $term));
            });
        }

        if (! empty($this->specializationIds)) {
            $query->whereHas('specializations', fn ($q) => $q->whereIn('specializations.id', $this->specializationIds));
        }

        if (! empty($this->marketplaceIds)) {
            $query->whereHas('marketplaces', fn ($q) => $q->whereIn('marketplaces.id', $this->marketplaceIds));
        }

        if ($this->rateMin !== null) {
            $query->where('hourly_rate', '>=', $this->rateMin);
        }

        if ($this->rateMax !== null) {
            $query->where('hourly_rate', '<=', $this->rateMax);
        }

        if ($this->minRating !== null) {
            $query->where('rating_avg', '>=', $this->minRating);
        }

        if ($this->country !== '') {
            $query->whereHas('user', fn ($q) => $q->where('country_code', mb_strtoupper($this->country)));
        }

        if ($this->verifiedOnly) {
            $query->where('is_verified', true);
        }

        $query = match ($this->sort) {
            'rating' => $query->orderByDesc('rating_avg')->orderByDesc('reviews_count'),
            'newest' => $query->orderByDesc('created_at'),
            default => $query->orderByDesc('is_verified')->orderByDesc('rating_avg')->orderByDesc('total_contracts_completed'),
        };

        return $query->paginate(20);
    }
}
