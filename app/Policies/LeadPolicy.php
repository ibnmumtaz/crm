<?php

namespace App\Policies;

use App\Models\Lead;
use App\Models\User;

class LeadPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('lead.view');
    }

    public function view(User $user, Lead $lead): bool
    {
        if (! $user->can('lead.view')) {
            return false;
        }
        if ($user->hasRole('admin') || $user->hasRole('super-admin')) {
            return true;
        }
        if ($user->hasRole('sales-manager')) {
            return $lead->team_id && $user->teams()->pluck('teams.id')->contains($lead->team_id);
        }

        return $lead->owner_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->can('lead.create');
    }

    public function update(User $user, Lead $lead): bool
    {
        if (! $user->can('lead.update')) {
            return false;
        }
        if ($user->hasRole('admin') || $user->hasRole('super-admin')) {
            return true;
        }
        if ($user->hasRole('sales-manager')) {
            return $lead->team_id && $user->teams()->pluck('teams.id')->contains($lead->team_id);
        }

        return $lead->owner_id === $user->id;
    }

    public function delete(User $user, Lead $lead): bool
    {
        if (! $user->can('lead.delete')) {
            return false;
        }
        if ($user->hasRole('admin') || $user->hasRole('super-admin')) {
            return true;
        }
        if ($user->hasRole('sales-manager')) {
            return $lead->team_id && $user->teams()->pluck('teams.id')->contains($lead->team_id);
        }

        return $lead->owner_id === $user->id;
    }

    public function restore(User $user, Lead $lead): bool
    {
        if (! $user->can('lead.restore')) {
            return false;
        }
        if ($user->hasRole('admin') || $user->hasRole('super-admin')) {
            return true;
        }
        if ($user->hasRole('sales-manager')) {
            return $lead->team_id && $user->teams()->pluck('teams.id')->contains($lead->team_id);
        }

        return $lead->owner_id === $user->id;
    }

    public function forceDelete(User $user, Lead $lead): bool
    {
        return $user->hasRole('super-admin');
    }

    public function reassign(User $user, Lead $lead): bool
    {
        if (! $user->can('lead.reassign')) {
            return false;
        }
        if ($user->hasRole('admin') || $user->hasRole('super-admin')) {
            return true;
        }
        if ($user->hasRole('sales-manager')) {
            return $lead->team_id && $user->teams()->pluck('teams.id')->contains($lead->team_id);
        }

        return false;
    }
}
