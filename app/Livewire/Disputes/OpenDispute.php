<?php

declare(strict_types=1);

namespace App\Livewire\Disputes;

use App\Models\Contract;
use App\Services\Reviews\DisputeService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use RuntimeException;

#[Layout('layouts.app')]
class OpenDispute extends Component
{
    public Contract $contract;

    public string $reasonCode = 'quality';

    public string $description = '';

    public function mount(Contract $contract): void
    {
        abort_unless(in_array(Auth::id(), [$contract->buyer_id, $contract->seller_id], true), 403);
        $this->contract = $contract;
    }

    public function open(DisputeService $service): void
    {
        $this->validate([
            'reasonCode' => ['required', 'in:quality,deadline,communication,scope,other'],
            'description' => ['required', 'string', 'min:30', 'max:2000'],
        ]);

        try {
            $service->open($this->contract, Auth::user(), [
                'reason_code' => $this->reasonCode,
                'description' => $this->description,
            ]);
            session()->flash('status', __('app.disputes.opened'));
            $this->redirect(route('contracts.show', $this->contract), navigate: true);
        } catch (RuntimeException $e) {
            $this->addError('description', $e->getMessage());
        }
    }

    public function render(): View
    {
        return view('livewire.disputes.open-dispute');
    }
}
