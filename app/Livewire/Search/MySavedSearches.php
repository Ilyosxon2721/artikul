<?php

declare(strict_types=1);

namespace App\Livewire\Search;

use App\Models\SavedSearch;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class MySavedSearches extends Component
{
    public function toggle(int $id): void
    {
        $search = SavedSearch::query()->where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $search->forceFill(['is_active' => ! $search->is_active])->save();
    }

    public function delete(int $id): void
    {
        SavedSearch::query()->where('id', $id)->where('user_id', Auth::id())->delete();
    }

    public function render(): View
    {
        return view('livewire.search.my-saved-searches', [
            'searches' => SavedSearch::query()
                ->where('user_id', Auth::id())
                ->latest()
                ->get(),
        ]);
    }
}
