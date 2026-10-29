<?php

namespace App\Http\Controllers\Web;

use App\Enums\LeadStatus;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\LeadStageHistory;
use App\Models\Pipeline;
use App\Models\PipelineStage;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class LeadController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('viewAny', Lead::class);

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

        $leads = $query->latest()->paginate(15)->withQueryString();

        return view('leads.index', compact('leads'));
    }

    public function export(Request $request)
    {
        $this->authorize('viewAny', Lead::class);

        $user = $request->user();
        $leads = Lead::visibleTo($user)->get();

        $filename = 'leads-'.now()->format('Ymd-His').'.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        $callback = function () use ($leads) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Name', 'Company', 'Email', 'Phone', 'Value', 'Status', 'Owner', 'Pipeline', 'Stage', 'Expected Close']);
            foreach ($leads as $lead) {
                fputcsv($handle, [
                    $lead->id,
                    $lead->name,
                    $lead->company,
                    $lead->email,
                    $lead->phone,
                    $lead->value,
                    $lead->status->value ?? $lead->status,
                    optional($lead->owner)->name,
                    optional($lead->pipeline)->name,
                    optional($lead->stage)->name,
                    optional($lead->expected_close)?->format('Y-m-d'),
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function kanban(Request $request)
    {
        $this->authorize('viewAny', Lead::class);

        $user = $request->user();
        $pipeline = Pipeline::with('stages')->first();
        $leads = Lead::visibleTo($user)->with('stage')->get()->groupBy('stage_id');

        return view('leads.kanban', compact('pipeline', 'leads'));
    }

    public function create()
    {
        $this->authorize('create', Lead::class);

        $pipelines = Pipeline::with('stages')->get();
        $sources = LeadSource::all();

        return view('leads.create', compact('pipelines', 'sources'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Lead::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'company' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'value' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|size:3',
            'pipeline_id' => 'required|exists:pipelines,id',
            'stage_id' => 'required|exists:pipeline_stages,id',
            'source_id' => 'nullable|exists:lead_sources,id',
            'expected_close' => 'nullable|date',
            'owner_id' => 'nullable|exists:users,id',
            'team_id' => 'nullable|exists:teams,id',
        ]);

        if (! empty($validated['email'])) {
            $dup = Lead::where('email', $validated['email'])->first();
            if ($dup) {
                return back()->withErrors(['email' => 'A lead with this email already exists.'])->withInput();
            }
        }

        $stage = PipelineStage::find($validated['stage_id']);
        if (! $stage || $stage->pipeline_id != $validated['pipeline_id']) {
            return back()->withErrors(['stage_id' => 'Stage does not belong to selected pipeline.'])->withInput();
        }

        $user = $request->user();

        // Resolve owner
        $ownerId = $validated['owner_id'] ?? $user->id;
        if ($ownerId != $user->id && ! $user->can('lead.reassign')) {
            return back()->withErrors(['owner_id' => 'You are not allowed to assign leads to another user.'])->withInput();
        }
        $owner = User::findOrFail($ownerId);
        if (! $this->canAssignOwner($user, $owner)) {
            return back()->withErrors(['owner_id' => 'You cannot assign this lead to the selected user.'])->withInput();
        }

        // Resolve team
        $teamId = $validated['team_id'] ?? null;
        if ($teamId !== null && ! $this->canAssignTeam($user, $teamId)) {
            return back()->withErrors(['team_id' => 'You cannot assign this lead to the selected team.'])->withInput();
        }
        if ($teamId === null && $owner->teams()->exists()) {
            // keep null unless provided
        }

        $validated['owner_id'] = $owner->id;
        $validated['team_id'] = $teamId;
        $validated['status'] = $validated['status'] ?? LeadStatus::Active->value;

        $lead = Lead::create($validated);

        LeadStageHistory::create([
            'lead_id' => $lead->id,
            'from_stage_id' => null,
            'to_stage_id' => $lead->stage_id,
            'user_id' => $user->id,
            'reason' => 'Lead created',
        ]);

        return redirect()->route('leads.index')->with('success', 'Lead created successfully.');
    }

    public function show(Lead $lead)
    {
        $this->authorize('view', $lead);

        $lead->load(['owner', 'team', 'pipeline', 'stage', 'source', 'communications.user', 'tasks', 'stageHistory.fromStage', 'stageHistory.toStage']);

        return view('leads.show', compact('lead'));
    }

    public function edit(Lead $lead)
    {
        $this->authorize('update', $lead);

        $pipelines = Pipeline::with('stages')->get();
        $sources = LeadSource::all();

        return view('leads.edit', compact('lead', 'pipelines', 'sources'));
    }

    public function update(Request $request, Lead $lead)
    {
        $this->authorize('update', $lead);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'nullable|email',
            'company' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'value' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|size:3',
            'status' => ['nullable', new Enum(LeadStatus::class)],
            'lost_reason' => 'nullable|string|max:255',
            'expected_close' => 'nullable|date',
            'pipeline_id' => 'nullable|exists:pipelines,id',
            'stage_id' => 'nullable|exists:pipeline_stages,id',
            'source_id' => 'nullable|exists:lead_sources,id',
        ]);

        $user = $request->user();

        // pipeline/stage consistency
        $newPipelineId = $validated['pipeline_id'] ?? $lead->pipeline_id;
        $newStageId = $validated['stage_id'] ?? $lead->stage_id;
        $stage = PipelineStage::find($newStageId);
        if (! $stage || $stage->pipeline_id != $newPipelineId) {
            return back()->withErrors(['stage_id' => 'Stage does not belong to selected pipeline.'])->withInput();
        }

        // terminal state handling
        if (isset($validated['status'])) {
            if ($validated['status'] === LeadStatus::Lost->value && empty($validated['lost_reason']) && empty($lead->lost_reason)) {
                // allow; lost_reason optional
            }
        }

        $oldStageId = $lead->stage_id;
        if (array_key_exists('stage_id', $validated) && $validated['stage_id'] != $oldStageId) {
            $stage = PipelineStage::find($validated['stage_id']);
            if ($stage?->is_won) {
                $validated['status'] = LeadStatus::Won->value;
            } elseif ($stage?->is_lost) {
                $validated['status'] = LeadStatus::Lost->value;
            } elseif ($lead->status?->isTerminal()) {
                $validated['status'] = LeadStatus::Active->value;
            }
        }
        $lead->update($validated);

        if (array_key_exists('stage_id', $validated) && $validated['stage_id'] != $oldStageId) {
            LeadStageHistory::create([
                'lead_id' => $lead->id,
                'from_stage_id' => $oldStageId,
                'to_stage_id' => $lead->stage_id,
                'user_id' => $user->id,
                'reason' => $request->input('reason'),
            ]);
        }

        return redirect()->route('leads.index')->with('success', 'Lead updated successfully.');
    }

    public function stage(Request $request, Lead $lead)
    {
        $this->authorize('update', $lead);

        $validated = $request->validate([
            'stage_id' => 'required|exists:pipeline_stages,id',
        ]);

        $stage = PipelineStage::find($validated['stage_id']);
        if (! $stage || $stage->pipeline_id != $lead->pipeline_id) {
            return response()->json(['message' => 'Stage does not belong to pipeline.'], 422);
        }

        $fromStage = $lead->stage_id;
        $stage = PipelineStage::find($validated['stage_id']);
        $updates = ['stage_id' => $validated['stage_id']];
        if ($stage?->is_won) {
            $updates['status'] = LeadStatus::Won;
        } elseif ($stage?->is_lost) {
            $updates['status'] = LeadStatus::Lost;
            if (! $lead->lost_reason && $request->filled('reason')) {
                $updates['lost_reason'] = $request->input('reason');
            }
        } elseif ($lead->status?->isTerminal()) {
            $updates['status'] = LeadStatus::Active;
        }
        $lead->update($updates);

        LeadStageHistory::create([
            'lead_id' => $lead->id,
            'from_stage_id' => $fromStage,
            'to_stage_id' => $validated['stage_id'],
            'user_id' => $request->user()->id,
            'reason' => $request->input('reason'),
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('leads.kanban')->with('success', 'Lead moved.');
    }

    public function destroy(Lead $lead)
    {
        $this->authorize('delete', $lead);
        $lead->delete();

        return redirect()->route('leads.index')->with('success', 'Lead deleted successfully.');
    }

    public function convert(Lead $lead)
    {
        $this->authorize('update', $lead);

        if ($lead->status !== LeadStatus::Won) {
            return back()->with('error', 'Only won leads can be converted.');
        }

        $existing = Customer::where('lead_id', $lead->id)->first();
        if ($existing) {
            return back()->with('error', 'Lead already converted.');
        }

        Customer::create([
            'lead_id' => $lead->id,
            'name' => $lead->name,
            'company' => $lead->company,
            'email' => $lead->email,
            'phone' => $lead->phone,
        ]);

        return redirect()->route('leads.show', $lead)->with('success', 'Lead converted to customer.');
    }

    private function canAssignOwner(User $actor, User $owner): bool
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

    private function canAssignTeam(User $actor, int $teamId): bool
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
