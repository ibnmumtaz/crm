<?php

namespace App\Policies;

use App\Models\Target;
use App\Models\User;

class TargetPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('target.view');
    }

    public function view(User $user, Target $target): bool
    {
        if (! $user->can('target.view')) {
            return false;
        }
        if ($user->hasRole('admin') || $user->hasRole('super-admin')) {
            return true;
        }
        if ($target->team_id && $user->teams()->pluck('teams.id')->contains($target->team_id)) {
            return true;
        }

        return $target->user_id === $user->id || $target->created_by === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->can('target.create');
    }

    public function update(User $user, Target $target): bool
    {
        if (! $user->can('target.update')) {
            return false;
        }
        if ($user->hasRole('admin') || $user->hasRole('super-admin')) {
            return true;
        }
        if ($target->team_id && $user->teams()->pluck('teams.id')->contains($target->team_id)) {
            return $user->hasRole('sales-manager');
        }

        return $target->created_by === $user->id;
    }

    public function delete(User $user, Target $target): bool
    {
        if (! $user->can('target.delete')) {
            return false;
        }
        if ($user->hasRole('admin') || $user->hasRole('super-admin')) {
            return true;
        }

        return $target->created_by === $user->id;
    }
}
