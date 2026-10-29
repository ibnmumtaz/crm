<?php

namespace App\Http\Controllers\Web;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class TaskController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('viewAny', Task::class);

        $user = $request->user();
        $query = Task::visibleTo($user);

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->string('priority'));
        }
        if ($request->filled('lead_id')) {
            $query->where('lead_id', $request->integer('lead_id'));
        }

        $tasks = $query->latest()->paginate(15)->withQueryString();

        return view('tasks.index', compact('tasks'));
    }

    public function create(Request $request)
    {
        $this->authorize('create', Task::class);

        $leads = Lead::visibleTo($request->user())->get();
        $users = $this->assignableUsers($request->user());

        return view('tasks.create', compact('leads', 'users'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Task::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'due_at' => 'required|date',
            'priority' => ['required', new Enum(TaskPriority::class)],
            'status' => ['nullable', new Enum(TaskStatus::class)],
            'lead_id' => 'nullable|exists:leads,id',
            'assignee_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
            'outcome' => 'nullable|string|max:255',
        ]);

        $user = $request->user();

        if (! empty($validated['lead_id'])) {
            $lead = Lead::findOrFail($validated['lead_id']);
            $this->authorize('view', $lead);
        }

        $assigneeId = $validated['assignee_id'] ?? $user->id;
        if ($assigneeId != $user->id && ! $this->canAssignTask($user, (int) $assigneeId)) {
            return back()->withErrors(['assignee_id' => 'You cannot assign a task to this user.'])->withInput();
        }

        $validated['assignee_id'] = $assigneeId;
        $validated['creator_id'] = $user->id;
        $validated['status'] = $validated['status'] ?? TaskStatus::Pending->value;

        Task::create($validated);

        return redirect()->route('tasks.index')->with('success', 'Task created successfully.');
    }

    public function show(Task $task)
    {
        $this->authorize('view', $task);

        $task->load(['lead', 'assignee', 'creator']);

        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        $this->authorize('update', $task);

        $leads = Lead::visibleTo(request()->user())->get();
        $users = $this->assignableUsers(request()->user());

        return view('tasks.edit', compact('task', 'leads', 'users'));
    }

    public function update(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'due_at' => 'sometimes|required|date',
            'priority' => ['sometimes', 'required', new Enum(TaskPriority::class)],
            'status' => ['sometimes', 'required', new Enum(TaskStatus::class)],
            'lead_id' => 'nullable|exists:leads,id',
            'assignee_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
            'outcome' => 'nullable|string|max:255',
        ]);

        $user = $request->user();

        if (array_key_exists('lead_id', $validated) && ! empty($validated['lead_id'])) {
            $lead = Lead::findOrFail($validated['lead_id']);
            $this->authorize('view', $lead);
        }

        if (array_key_exists('assignee_id', $validated) && $validated['assignee_id'] !== null && (int) $validated['assignee_id'] !== $task->assignee_id) {
            if (! $this->canAssignTask($user, (int) $validated['assignee_id'])) {
                return back()->withErrors(['assignee_id' => 'You cannot assign a task to this user.'])->withInput();
            }
        }

        // keep completion timestamp consistent
        if (isset($validated['status'])) {
            if ($validated['status'] === TaskStatus::Completed->value && ! $task->completed_at) {
                $validated['completed_at'] = now();
            }
            if ($validated['status'] !== TaskStatus::Completed->value) {
                $validated['completed_at'] = null;
            }
        }

        $task->update($validated);

        return redirect()->route('tasks.index')->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);
        $task->delete();

        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully.');
    }

    public function complete(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $validated = $request->validate([
            'outcome' => 'nullable|string|max:255',
        ]);

        $task->update([
            'status' => TaskStatus::Completed->value,
            'completed_at' => now(),
            'outcome' => $validated['outcome'] ?? $request->input('outcome'),
        ]);

        return redirect()->route('tasks.show', $task)->with('success', 'Task completed.');
    }

    public function cancel(Task $task)
    {
        $this->authorize('update', $task);

        $task->update([
            'status' => TaskStatus::Cancelled->value,
            'completed_at' => null,
        ]);

        return redirect()->route('tasks.show', $task)->with('success', 'Task cancelled.');
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

    private function canAssignTask(User $actor, int $assigneeId): bool
    {
        if ($actor->hasRole('admin') || $actor->hasRole('super-admin')) {
            return true;
        }
        if ($actor->hasRole('sales-manager')) {
            $teamIds = $actor->teams()->pluck('teams.id');
            $ids = Team::whereIn('id', $teamIds)->with('users')->get()->pluck('users')->flatten()->pluck('id')->unique();

            return $ids->contains($assigneeId);
        }

        return $assigneeId === $actor->id;
    }
}
