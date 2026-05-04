<?php

declare(strict_types=1);

namespace App\Livewire\Verification;

use App\Enums\VerificationStatus;
use App\Models\Verification;
use App\Services\Verification\VerificationService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use RuntimeException;

#[Layout('layouts.app')]
class SubmitVerification extends Component
{
    use WithFileUploads;

    public $idDocument;

    public $selfie;

    /** @var array<int, mixed> */
    public array $marketplaceScreenshots = [];

    public $recommendation;

    public string $passportNumber = '';

    public function submit(VerificationService $service): void
    {
        $user = Auth::user();
        abort_unless($user !== null, 403);

        $this->validate([
            'idDocument' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:8192'],
            'selfie' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:8192'],
            'marketplaceScreenshots' => ['nullable', 'array', 'max:10'],
            'marketplaceScreenshots.*' => ['file', 'mimes:jpg,jpeg,png,pdf', 'max:8192'],
            'recommendation' => ['nullable', 'file', 'mimes:pdf,docx,jpg,jpeg,png', 'max:8192'],
            'passportNumber' => ['nullable', 'string', 'max:32'],
        ]);

        try {
            $service->submit(
                $user,
                $this->idDocument,
                $this->selfie,
                $this->marketplaceScreenshots,
                $this->recommendation,
                $this->passportNumber !== '' ? $this->passportNumber : null,
            );
            session()->flash('status', __('app.verification.submitted'));
            $this->redirect(route('verification.show'), navigate: true);
        } catch (RuntimeException $e) {
            $this->addError('passportNumber', $e->getMessage());
        }
    }

    public function render(): View
    {
        $user = Auth::user();
        $latest = $user !== null
            ? Verification::query()->where('user_id', $user->id)->latest()->first()
            : null;

        return view('livewire.verification.submit-verification', [
            'latest' => $latest,
            'pendingStatus' => VerificationStatus::Pending,
            'approvedStatus' => VerificationStatus::Approved,
            'rejectedStatus' => VerificationStatus::Rejected,
        ]);
    }
}
