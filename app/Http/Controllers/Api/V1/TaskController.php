<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Task;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request)
    {
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

        $perPage = min((int) ($request->per_page ?? 25), 100);
        $tasks = $query->latest()->paginate($perPage);

        return response()->json(['data' => $tasks->items(), 'meta' => ['total' => $tasks->total(), 'current_page' => $tasks->currentPage(), 'last_page' => $tasks->lastPage()]]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'due_at' => 'required|date',
            'priority' => ['nullable', new Enum(TaskPriority::class)],
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
        if ($assigneeId != $user->id) {
            if (! $user->hasRole('admin') && ! $user->hasRole('super-admin') && ! $user->hasRole('sales-manager')) {
                return response()->json(['message' => 'Forbidden.'], 403);
            }
            if ($user->hasRole('sales-manager')) {
                $teamIds = $user->teams()->pluck('teams.id');
                $ids = Team::whereIn('id', $teamIds)->with('users')->get()->pluck('users')->flatten()->pluck('id')->unique();
                if (! $ids->contains($assigneeId)) {
                    return response()->json(['message' => 'Cannot assign task to selected user.'], 422);
                }
            }
        }

        $validated['assignee_id'] = $assigneeId;
        $validated['creator_id'] = $user->id;
        $validated['priority'] = $validated['priority'] ?? TaskPriority::Medium->value;
        $validated['status'] = $validated['status'] ?? TaskStatus::Pending->value;

        $task = Task::create($validated);

        return response()->json(['data' => $task], 201);
    }

    public function show(Task $task)
    {
        $this->authorize('view', $task);

        return response()->json(['data' => $task->load(['lead', 'assignee', 'creator'])]);
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

        if (array_key_exists('lead_id', $validated) && ! empty($validated['lead_id'])) {
            $lead = Lead::findOrFail($validated['lead_id']);
            $this->authorize('view', $lead);
        }

        if (array_key_exists('assignee_id', $validated) && $validated['assignee_id'] !== null && (int) $validated['assignee_id'] !== $task->assignee_id) {
            $user = $request->user();
            if (! $user->hasRole('admin') && ! $user->hasRole('super-admin') && ! $user->hasRole('sales-manager')) {
                return response()->json(['message' => 'Forbidden.'], 403);
            }
        }

        if (isset($validated['status'])) {
            if ($validated['status'] === TaskStatus::Completed->value && ! $task->completed_at) {
                $validated['completed_at'] = now();
            }
            if ($validated['status'] !== TaskStatus::Completed->value) {
                $validated['completed_at'] = null;
            }
        }

        $task->update($validated);

        return response()->json(['data' => $task]);
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);
        $task->delete();

        return response()->json(null, 204);
    }

    public function complete(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $validated = $request->validate(['outcome' => 'nullable|string|max:255']);

        $task->update([
            'status' => TaskStatus::Completed->value,
            'completed_at' => now(),
            'outcome' => $validated['outcome'] ?? $request->input('outcome'),
        ]);

        return response()->json(['data' => $task]);
    }

    public function cancel(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $task->update([
            'status' => TaskStatus::Cancelled->value,
            'completed_at' => null,
        ]);

        return response()->json(['data' => $task]);
    }

    public function calendar(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
        ]);

        $from = $validated['from'] ?? now()->startOfMonth();
        $to = $validated['to'] ?? now()->endOfMonth();

        $tasks = Task::visibleTo($user)
            ->whereBetween('due_at', [$from, $to])
            ->orderBy('due_at')
            ->limit(500)
            ->get();

        return response()->json(['data' => $tasks]);
    }
}
