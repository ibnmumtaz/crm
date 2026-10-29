<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\Team;
use App\Models\User;

class TaskPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('task.view');
    }

    public function view(User $user, Task $task): bool
    {
        if (! $user->can('task.view')) {
            return false;
        }
        if ($user->hasRole('admin') || $user->hasRole('super-admin')) {
            return true;
        }
        if ($user->hasRole('sales-manager')) {
            $teamIds = $user->teams()->pluck('teams.id');
            $teamUserIds = Team::whereIn('id', $teamIds)->with('users')->get()->pluck('users')->flatten()->pluck('id')->unique();

            return $teamUserIds->contains($task->assignee_id) || $teamUserIds->contains($task->creator_id);
        }

        return $task->assignee_id === $user->id || $task->creator_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->can('task.create');
    }

    public function update(User $user, Task $task): bool
    {
        if (! $user->can('task.update')) {
            return false;
        }
        if ($user->hasRole('admin') || $user->hasRole('super-admin')) {
            return true;
        }
        if ($user->hasRole('sales-manager')) {
            $teamIds = $user->teams()->pluck('teams.id');
            $teamUserIds = Team::whereIn('id', $teamIds)->with('users')->get()->pluck('users')->flatten()->pluck('id')->unique();

            return $teamUserIds->contains($task->assignee_id) || $teamUserIds->contains($task->creator_id);
        }

        return $task->assignee_id === $user->id || $task->creator_id === $user->id;
    }

    public function delete(User $user, Task $task): bool
    {
        if (! $user->can('task.delete')) {
            return false;
        }
        if ($user->hasRole('admin') || $user->hasRole('super-admin')) {
            return true;
        }
        if ($user->hasRole('sales-manager')) {
            $teamIds = $user->teams()->pluck('teams.id');
            $teamUserIds = Team::whereIn('id', $teamIds)->with('users')->get()->pluck('users')->flatten()->pluck('id')->unique();

            return $teamUserIds->contains($task->assignee_id) || $teamUserIds->contains($task->creator_id);
        }

        return $task->creator_id === $user->id;
    }
}
