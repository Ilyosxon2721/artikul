<?php

declare(strict_types=1);

namespace App\Livewire\Profile;

use App\Models\UserProfile;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class NotificationPreferences extends Component
{
    /** @var array<string, array<string, bool>> */
    public array $prefs = [];

    public string $frequency = 'instant';

    /** @var array<string, string> Default categories */
    private const CATEGORIES = [
        'tasks' => 'tasks',
        'proposals' => 'proposals',
        'contracts' => 'contracts',
        'chat' => 'chat',
        'reviews' => 'reviews',
        'disputes' => 'disputes',
    ];

    private const CHANNELS = ['email', 'telegram', 'in_app'];

    public function mount(): void
    {
        $user = Auth::user();
        abort_unless($user !== null, 403);

        $profile = UserProfile::firstOrCreate(['user_id' => $user->id]);
        $stored = (array) ($profile->notification_preferences ?? []);
        $this->frequency = (string) ($stored['frequency'] ?? 'instant');

        foreach (self::CATEGORIES as $key => $_) {
            $this->prefs[$key] = [
                'email' => (bool) ($stored['categories'][$key]['email'] ?? true),
                'telegram' => (bool) ($stored['categories'][$key]['telegram'] ?? false),
                'in_app' => (bool) ($stored['categories'][$key]['in_app'] ?? true),
            ];
        }
    }

    public function save(): void
    {
        $user = Auth::user();
        abort_unless($user !== null, 403);

        $this->validate([
            'frequency' => ['required', 'in:instant,daily,important_only'],
        ]);

        $profile = UserProfile::firstOrCreate(['user_id' => $user->id]);
        $profile->forceFill([
            'notification_preferences' => [
                'frequency' => $this->frequency,
                'categories' => $this->prefs,
            ],
        ])->save();

        session()->flash('status', __('app.notifications_prefs.saved'));
    }

    public function render(): View
    {
        return view('livewire.profile.notification-preferences', [
            'categories' => array_keys(self::CATEGORIES),
            'channels' => self::CHANNELS,
        ]);
    }
}
