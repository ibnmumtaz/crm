<?php

namespace App\Policies;

use App\Models\Communication;
use App\Models\User;

class CommunicationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('communication.view');
    }

    public function view(User $user, Communication $communication): bool
    {
        if (! $user->can('communication.view')) {
            return false;
        }
        if ($user->hasRole('admin') || $user->hasRole('super-admin')) {
            return true;
        }
        if ($communication->lead_id) {
            $lead = $communication->lead;
            if ($lead && $user->hasRole('sales-manager')) {
                return $lead->team_id && $user->teams()->pluck('teams.id')->contains($lead->team_id);
            }
            if ($lead && $lead->owner_id === $user->id) {
                return true;
            }
        }

        return $communication->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->can('communication.create');
    }

    public function update(User $user, Communication $communication): bool
    {
        if (! $user->can('communication.update')) {
            return false;
        }
        if ($communication->editable_until && $communication->editable_until->lt(now())) {
            return false;
        }
        if ($user->hasRole('admin') || $user->hasRole('super-admin')) {
            return true;
        }

        return $communication->user_id === $user->id;
    }

    public function delete(User $user, Communication $communication): bool
    {
        if (! $user->can('communication.delete')) {
            return false;
        }
        if ($user->hasRole('admin') || $user->hasRole('super-admin')) {
            return true;
        }

        return $communication->user_id === $user->id;
    }
}
