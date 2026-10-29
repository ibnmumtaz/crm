<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    use AuthorizesRequests;

    private function ensureAdmin(): void
    {
        if (! auth()->user()->can('user.manage')) {
            abort(403);
        }
    }

    public function index()
    {
        $this->ensureAdmin();
        $teams = Team::withCount('users')->latest()->get();

        return view('admin.teams.index', compact('teams'));
    }

    public function create()
    {
        $this->ensureAdmin();
        $users = User::orderBy('name')->get();

        return view('admin.teams.create', compact('users'));
    }

    public function store(Request $request)
    {
        $this->ensureAdmin();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'users' => 'nullable|array',
            'users.*' => 'exists:users,id',
        ]);

        $team = Team::create(['name' => $validated['name']]);
        if (! empty($validated['users'])) {
            $team->users()->sync($validated['users']);
        }

        return redirect()->route('admin.teams.index')->with('success', 'Team created.');
    }

    public function show(Team $team)
    {
        $this->ensureAdmin();
        $team->load('users');

        return view('admin.teams.show', compact('team'));
    }

    public function edit(Team $team)
    {
        $this->ensureAdmin();
        $users = User::orderBy('name')->get();

        return view('admin.teams.edit', compact('team', 'users'));
    }

    public function update(Request $request, Team $team)
    {
        $this->ensureAdmin();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'users' => 'nullable|array',
            'users.*' => 'exists:users,id',
        ]);

        $team->update(['name' => $validated['name']]);
        $team->users()->sync($validated['users'] ?? []);

        return redirect()->route('admin.teams.index')->with('success', 'Team updated.');
    }

    public function destroy(Team $team)
    {
        $this->ensureAdmin();
        if ($team->users()->exists()) {
            return back()->with('error', 'Remove users from team before deleting.');
        }
        $team->delete();

        return redirect()->route('admin.teams.index')->with('success', 'Team deleted.');
    }
}
