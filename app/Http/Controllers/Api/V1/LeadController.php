<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Lead;
use App\Models\LeadStageHistory;
use App\Models\PipelineStage;
use App\Models\User;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $query = Lead::visibleTo($user);

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('company', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }
        if ($request->filled('pipeline_id')) {
            $query->where('pipeline_id', $request->integer('pipeline_id'));
        }
        if ($request->filled('stage_id')) {
            $query->where('stage_id', $request->integer('stage_id'));
        }
        if ($request->filled('owner_id')) {
            $query->where('owner_id', $request->integer('owner_id'));
        }

        $perPage = min((int) ($request->per_page ?? 25), 100);
        $leads = $query->latest()->paginate($perPage);

        return response()->json([
            'data' => $leads->items(),
            'meta' => [
                'current_page' => $leads->currentPage(),
                'last_page' => $leads->lastPage(),
                'per_page' => $leads->perPage(),
                'total' => $leads->total(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company' => 'nullable|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'value' => 'nullable|numeric',
            'currency' => 'nullable|string|size:3',
            'pipeline_id' => 'required|exists:pipelines,id',
            'stage_id' => 'required|exists:pipeline_stages,id',
            'source_id' => 'nullable|exists:lead_sources,id',
            'expected_close' => 'nullable|date',
            'owner_id' => 'nullable|exists:users,id',
            'team_id' => 'nullable|exists:teams,id',
        ]);

        $stage = PipelineStage::find($validated['stage_id']);
        if (! $stage || $stage->pipeline_id != $validated['pipeline_id']) {
            return response()->json(['message' => 'Stage does not belong to selected pipeline.'], 422);
        }

        $user = $request->user();

        $ownerId = $validated['owner_id'] ?? $user->id;
        if ($ownerId != $user->id && ! $user->can('lead.reassign')) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }
        $owner = User::findOrFail($ownerId);
        if (! $this->canAssignOwner($user, $owner)) {
            return response()->json(['message' => 'Cannot assign lead to selected user.'], 422);
        }

        $teamId = $validated['team_id'] ?? null;
        if ($teamId !== null && ! $this->canAssignTeam($user, $teamId)) {
            return response()->json(['message' => 'Cannot assign lead to selected team.'], 422);
        }

        $validated['owner_id'] = $owner->id;
        $validated['team_id'] = $teamId;
        $validated['status'] = $validated['status'] ?? 'active';

        $lead = Lead::create($validated);

        LeadStageHistory::create([
            'lead_id' => $lead->id,
            'from_stage_id' => null,
            'to_stage_id' => $lead->stage_id,
            'user_id' => $user->id,
            'reason' => 'Lead created',
        ]);

        return response()->json(['data' => $lead], 201);
    }

    public function show(Lead $lead)
    {
        $this->authorize('view', $lead);

        return response()->json(['data' => $lead->load(['owner', 'stage', 'pipeline', 'source'])]);
    }

    public function update(Request $request, Lead $lead)
    {
        $this->authorize('update', $lead);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'company' => 'nullable|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'value' => 'nullable|numeric',
            'currency' => 'nullable|string|size:3',
            'status' => 'nullable|string|in:active,won,lost,archived',
            'lost_reason' => 'nullable|string|max:255',
            'expected_close' => 'nullable|date',
            'pipeline_id' => 'nullable|exists:pipelines,id',
            'stage_id' => 'nullable|exists:pipeline_stages,id',
            'source_id' => 'nullable|exists:lead_sources,id',
        ]);

        $newPipelineId = $validated['pipeline_id'] ?? $lead->pipeline_id;
        $newStageId = $validated['stage_id'] ?? $lead->stage_id;
        if (isset($validated['pipeline_id']) || isset($validated['stage_id'])) {
            $stage = PipelineStage::find($newStageId);
            if (! $stage || $stage->pipeline_id != $newPipelineId) {
                return response()->json(['message' => 'Stage does not belong to selected pipeline.'], 422);
            }
        }

        $oldStageId = $lead->stage_id;
        $lead->update($validated);

        if (isset($validated['stage_id']) && $validated['stage_id'] != $oldStageId) {
            LeadStageHistory::create([
                'lead_id' => $lead->id,
                'from_stage_id' => $oldStageId,
                'to_stage_id' => $lead->stage_id,
                'user_id' => $request->user()->id,
                'reason' => $request->input('reason'),
            ]);
        }

        return response()->json(['data' => $lead]);
    }

    public function destroy(Lead $lead)
    {
        $this->authorize('delete', $lead);
        $lead->delete();

        return response()->json(null, 204);
    }

    public function restore($id)
    {
        $lead = Lead::withTrashed()->findOrFail($id);
        $this->authorize('restore', $lead);
        $lead->restore();

        return response()->json(['data' => $lead]);
    }

    public function reassign(Request $request, Lead $lead)
    {
        $this->authorize('reassign', $lead);

        $validated = $request->validate(['owner_id' => 'required|exists:users,id']);
        $newOwner = User::findOrFail($validated['owner_id']);

        if (! $this->canAssignOwner($request->user(), $newOwner)) {
            return response()->json(['message' => 'Cannot assign lead to selected user.'], 422);
        }

        // team consistency: if lead has team, new owner should belong to it (for managers)
        $oldOwner = $lead->owner_id;
        $lead->update(['owner_id' => $newOwner->id]);

        Activity::create([
            'actor_id' => $request->user()->id,
            'action' => 'lead.reassigned',
            'subject_type' => Lead::class,
            'subject_id' => $lead->id,
            'properties' => ['old_owner' => $oldOwner, 'new_owner' => $lead->owner_id],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json(['data' => $lead]);
    }

    public function stage(Request $request, Lead $lead)
    {
        $this->authorize('update', $lead);

        $validated = $request->validate(['stage_id' => 'required|exists:pipeline_stages,id']);
        $stage = PipelineStage::find($validated['stage_id']);
        if (! $stage || $stage->pipeline_id != $lead->pipeline_id) {
            return response()->json(['message' => 'Stage does not belong to selected pipeline.'], 422);
        }

        $fromStage = $lead->stage_id;
        $lead->update(['stage_id' => $validated['stage_id']]);

        LeadStageHistory::create([
            'lead_id' => $lead->id,
            'from_stage_id' => $fromStage,
            'to_stage_id' => $validated['stage_id'],
            'user_id' => $request->user()->id,
            'reason' => $request->input('reason'),
        ]);

        return response()->json(['data' => $lead->load('stage')]);
    }

    public function timeline(Lead $lead)
    {
        $this->authorize('view', $lead);
        $communications = $lead->communications()->latest('occurred_at')->get();
        $history = $lead->stageHistory()->latest()->get();

        return response()->json(['data' => ['communications' => $communications, 'stage_history' => $history]]);
    }

    private function canAssignOwner($actor, $owner): bool
    {
        if ($actor->hasRole('admin') || $actor->hasRole('super-admin')) {
            return true;
        }
        if ($actor->hasRole('sales-manager')) {
            $actorTeams = $actor->teams()->pluck('teams.id');

            return $owner->teams()->whereIn('teams.id', $actorTeams)->exists() || $owner->id === $actor->id;
        }

        return $owner->id === $actor->id;
    }

    private function canAssignTeam($actor, int $teamId): bool
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
