<?php

declare(strict_types=1);

namespace App\Livewire\Search;

use App\Models\SavedSearch;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SaveSearch extends Component
{
    public bool $open = false;

    public string $name = '';

    public string $frequency = 'instant';

    /** @var array<string> */
    public array $notifyVia = ['email'];

    /** @var array<string, mixed> */
    public array $filters = [];

    public string $query = '';

    public function show(): void
    {
        $this->open = true;
    }

    public function close(): void
    {
        $this->open = false;
    }

    public function save(): void
    {
        $user = Auth::user();
        abort_unless($user !== null, 403);

        $this->validate([
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'frequency' => ['required', 'in:instant,daily,weekly'],
            'notifyVia' => ['array', 'min:1'],
            'notifyVia.*' => ['in:email,telegram'],
        ]);

        SavedSearch::create([
            'user_id' => $user->id,
            'name' => $this->name,
            'search_type' => 'tasks',
            'filters' => $this->filters,
            'query' => $this->query !== '' ? $this->query : null,
            'notify_via' => $this->notifyVia,
            'frequency' => $this->frequency,
            'is_active' => true,
        ]);

        $this->open = false;
        session()->flash('status', __('app.saved_searches.saved'));
        $this->dispatch('saved-search-created');
    }

    public function render(): View
    {
        return view('livewire.search.save-search');
    }
}
