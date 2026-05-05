<?php

declare(strict_types=1);

namespace App\Livewire\Contracts;

use App\Models\Contract;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class ContractsList extends Component
{
    use WithPagination;

    public function render(): View
    {
        $userId = Auth::id();

        $contracts = Contract::query()
            ->with(['task', 'buyer', 'seller'])
            ->where(function ($q) use ($userId): void {
                $q->where('buyer_id', $userId)->orWhere('seller_id', $userId);
            })
            ->latest()
            ->paginate(20);

        return view('livewire.contracts.contracts-list', [
            'contracts' => $contracts,
        ]);
    }
}
