<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\ReactivationNotification;
use Illuminate\Console\Command;

class SendReactivationEmails extends Command
{
    protected $signature = 'artikul:send-reactivation-emails';

    protected $description = 'Email users who have not been active for 30+ days.';

    public function handle(): int
    {
        $cutoff = now()->subDays(30);

        User::query()
            ->where('is_banned', false)
            ->whereNotNull('email_verified_at')
            ->where(function ($q) use ($cutoff): void {
                $q->whereNull('last_active_at')->orWhere('last_active_at', '<=', $cutoff);
            })
            ->where('created_at', '<=', $cutoff)
            ->whereDoesntHave('notifications', function ($q): void {
                $q->where('type', ReactivationNotification::class)
                    ->where('created_at', '>=', now()->subDays(30));
            })
            ->chunkById(100, function ($users): void {
                foreach ($users as $user) {
                    $user->notify(new ReactivationNotification);
                }
            });

        return Command::SUCCESS;
    }
}
