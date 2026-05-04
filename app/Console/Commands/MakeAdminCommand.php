<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MakeAdminCommand extends Command
{
    protected $signature = 'artikul:make-admin {email : Email of an existing user} {--super : Grant super-admin}';

    protected $description = 'Promote an existing user to admin (or super admin).';

    public function handle(): int
    {
        $email = (string) $this->argument('email');

        $user = User::query()->where('email', $email)->first();
        if ($user === null) {
            $this->error("User with email {$email} not found.");

            return Command::FAILURE;
        }

        $user->forceFill([
            'is_admin' => true,
            'is_super_admin' => (bool) $this->option('super'),
        ])->save();

        $this->info("{$user->name} is now ".($this->option('super') ? 'super admin' : 'admin').'.');

        return Command::SUCCESS;
    }
}
