<?php

namespace App\Http\Controllers\Web;

use App\Enums\TargetPeriod;
use App\Enums\TargetType;
use App\Http\Controllers\Controller;
use App\Models\Target;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class TargetController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('viewAny', Target::class);

        $targets = Target::visibleTo($request->user())->latest()->get();

        return view('targets.index', compact('targets'));
    }

    public function create(Request $request)
    {
        $this->authorize('create', Target::class);

        $users = $this->assignableUsers($request->user());
        $teams = $this->assignableTeams($request->user());

        return view('targets.create', compact('users', 'teams'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Target::class);

        $validated = $request->validate([
            'type' => ['required', new Enum(TargetType::class)],
            'period' => ['required', new Enum(TargetPeriod::class)],
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'amount' => 'required|numeric|min:0',
            'user_id' => 'nullable|exists:users,id',
            'team_id' => 'nullable|exists:teams,id',
        ]);

        $user = $request->user();

        if (! empty($validated['user_id']) && ! $this->canAssignTargetUser($user, (int) $validated['user_id'])) {
            return back()->withErrors(['user_id' => 'You cannot create a target for this user.'])->withInput();
        }
        if (! empty($validated['team_id']) && ! $this->canAssignTargetTeam($user, (int) $validated['team_id'])) {
            return back()->withErrors(['team_id' => 'You cannot create a target for this team.'])->withInput();
        }

        $validated['created_by'] = $user->id;
        if (empty($validated['user_id']) && empty($validated['team_id'])) {
            $validated['user_id'] = $user->id;
        }

        Target::create($validated);

        return redirect()->route('targets.index')->with('success', 'Target created successfully.');
    }

    public function show(Target $target)
    {
        $this->authorize('view', $target);

        return view('targets.show', compact('target'));
    }

    public function edit(Target $target)
    {
        $this->authorize('update', $target);

        $users = $this->assignableUsers(request()->user());
        $teams = $this->assignableTeams(request()->user());

        return view('targets.edit', compact('target', 'users', 'teams'));
    }

    public function update(Request $request, Target $target)
    {
        $this->authorize('update', $target);

        $validated = $request->validate([
            'type' => ['required', new Enum(TargetType::class)],
            'period' => ['required', new Enum(TargetPeriod::class)],
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'amount' => 'required|numeric|min:0',
            'user_id' => 'nullable|exists:users,id',
            'team_id' => 'nullable|exists:teams,id',
        ]);

        $user = $request->user();

        if (! empty($validated['user_id']) && ! $this->canAssignTargetUser($user, (int) $validated['user_id'])) {
            return back()->withErrors(['user_id' => 'You cannot assign this target to this user.'])->withInput();
        }
        if (! empty($validated['team_id']) && ! $this->canAssignTargetTeam($user, (int) $validated['team_id'])) {
            return back()->withErrors(['team_id' => 'You cannot assign this target to this team.'])->withInput();
        }

        $target->update($validated);

        return redirect()->route('targets.index')->with('success', 'Target updated successfully.');
    }

    public function destroy(Target $target)
    {
        $this->authorize('delete', $target);
        $target->delete();

        return redirect()->route('targets.index')->with('success', 'Target deleted successfully.');
    }

    private function assignableUsers(User $user)
    {
        if ($user->hasRole('admin') || $user->hasRole('super-admin')) {
            return User::orderBy('name')->get();
        }
        if ($user->hasRole('sales-manager')) {
            $teamIds = $user->teams()->pluck('teams.id');
            $ids = Team::whereIn('id', $teamIds)->with('users')->get()->pluck('users')->flatten()->pluck('id')->unique();

            return User::whereIn('id', $ids)->orderBy('name')->get();
        }

        return collect([$user]);
    }

    private function assignableTeams(User $user)
    {
        if ($user->hasRole('admin') || $user->hasRole('super-admin')) {
            return Team::orderBy('name')->get();
        }
        if ($user->hasRole('sales-manager')) {
            return $user->teams;
        }

        return collect();
    }

    private function canAssignTargetUser(User $actor, int $userId): bool
    {
        if ($actor->hasRole('admin') || $actor->hasRole('super-admin')) {
            return true;
        }
        if ($actor->hasRole('sales-manager')) {
            $teamIds = $actor->teams()->pluck('teams.id');
            $ids = Team::whereIn('id', $teamIds)->with('users')->get()->pluck('users')->flatten()->pluck('id')->unique();

            return $ids->contains($userId);
        }

        return $userId === $actor->id;
    }

    private function canAssignTargetTeam(User $actor, int $teamId): bool
    {
        if ($actor->hasRole('admin') || $actor->hasRole('super-admin')) {
            return true;
        }
        if ($actor->hasRole('sales-manager')) {
            return $actor->teams()->where('teams.id', $teamId)->exists();
        }

        return false;
    }
}
