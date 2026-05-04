<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Notifications\WelcomeNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendWelcomeOnRegistration implements ShouldQueue
{
    public function handle(Registered $event): void
    {
        $user = $event->user;
        if (! method_exists($user, 'notify')) {
            return;
        }

        $user->notify(new WelcomeNotification);
    }
}
