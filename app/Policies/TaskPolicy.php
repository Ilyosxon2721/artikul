<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Task $task): bool
    {
        if ($task->status === TaskStatus::Published) {
            return true;
        }

        return $user !== null && $user->id === $task->buyer_id;
    }

    public function create(User $user): bool
    {
        return ! $user->is_banned;
    }

    public function update(User $user, Task $task): bool
    {
        if ($user->id !== $task->buyer_id) {
            return false;
        }

        return in_array($task->status, [TaskStatus::Draft, TaskStatus::Published], true);
    }

    public function delete(User $user, Task $task): bool
    {
        return $user->id === $task->buyer_id && $task->status === TaskStatus::Draft;
    }
}
