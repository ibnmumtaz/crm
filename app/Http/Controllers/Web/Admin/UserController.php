<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    use AuthorizesRequests;

    private function ensureAdmin(): void
    {
        if (! auth()->user()->can('user.manage')) {
            abort(403);
        }
    }

    public function index(Request $request)
    {
        $this->ensureAdmin();
        $search = $request->string('search');
        $query = User::latest();
        if ($search->isNotEmpty()) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }
        $users = $query->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $this->ensureAdmin();
        $roles = Role::orderBy('name')->get();

        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $this->ensureAdmin();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|exists:roles,name',
            'status' => 'nullable|string|in:active,suspended',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'status' => $validated['status'] ?? 'active',
            'email_verified_at' => now(),
        ]);
        $user->assignRole($validated['role']);

        return redirect()->route('admin.users.index')->with('success', 'User created.');
    }

    public function show(User $user)
    {
        $this->ensureAdmin();
        $user->load('roles');

        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $this->ensureAdmin();
        $roles = Role::orderBy('name')->get();

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $this->ensureAdmin();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'role' => 'required|exists:roles,name',
            'status' => 'nullable|string|in:active,suspended',
            'password' => 'nullable|string|min:8',
        ]);

        // protect last admin
        $adminCount = User::role(['admin', 'super-admin'])->count();
        $removingAdmin = $user->hasRole('admin') || $user->hasRole('super-admin');
        $newIsAdmin = in_array($validated['role'], ['admin', 'super-admin']);
        if ($removingAdmin && ! $newIsAdmin && $adminCount <= 1) {
            return back()->with('error', 'Cannot remove the last administrator.')->withInput();
        }
        if ($removingAdmin && ($validated['status'] ?? 'active') !== 'active' && $adminCount <= 1) {
            return back()->with('error', 'Cannot deactivate the last administrator.')->withInput();
        }

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'status' => $validated['status'] ?? $user->status,
        ]);
        if (! empty($validated['password'])) {
            $user->update(['password' => bcrypt($validated['password'])]);
        }
        $user->syncRoles([$validated['role']]);

        return redirect()->route('admin.users.index')->with('success', 'User updated.');
    }

    public function destroy(User $user)
    {
        $this->ensureAdmin();
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Cannot delete your own account.');
        }
        $adminCount = User::role(['admin', 'super-admin'])->count();
        if (($user->hasRole('admin') || $user->hasRole('super-admin')) && $adminCount <= 1) {
            return back()->with('error', 'Cannot delete the last administrator.');
        }
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted.');
    }
}
