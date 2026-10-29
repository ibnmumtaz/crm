<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Target;
use App\Models\Task;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function summary(Request $request)
    {
        $user = $request->user();
        $leads = Lead::visibleTo($user);

        return response()->json([
            'data' => [
                'total_leads' => (clone $leads)->count(),
                'won_leads' => (clone $leads)->where('status', 'won')->count(),
                'pipeline_value' => (clone $leads)->whereNotIn('status', ['won', 'lost'])->sum('value'),
                'pending_tasks' => Task::visibleTo($user)->where('assignee_id', $user->id)->where('status', 'pending')->count(),
                'overdue_tasks' => Task::visibleTo($user)->where('assignee_id', $user->id)->where('status', 'overdue')->count(),
            ],
        ]);
    }

    public function pipelineValue(Request $request)
    {
        $user = $request->user();
        $value = Lead::visibleTo($user)
            ->whereNotIn('status', ['won', 'lost'])
            ->select('stage_id', \DB::raw('SUM(value) as total'))
            ->groupBy('stage_id')
            ->with('stage')
            ->get()
            ->mapWithKeys(fn ($l) => [optional($l->stage)->name => (float) $l->total]);

        return response()->json(['data' => ['pipeline_value' => $value, 'currency' => 'USD']]);
    }

    public function winRate(Request $request)
    {
        $user = $request->user();
        $total = Lead::visibleTo($user)->whereIn('status', ['won', 'lost'])->count();
        $won = Lead::visibleTo($user)->where('status', 'won')->count();
        $rate = $total > 0 ? round(($won / $total) * 100, 2) : 0;

        return response()->json(['data' => ['win_rate' => $rate, 'won' => $won, 'total_closed' => $total]]);
    }

    public function leadSourcePerformance(Request $request)
    {
        $user = $request->user();
        $data = Lead::visibleTo($user)
            ->select('source_id', \DB::raw('COUNT(*) as count'))
            ->groupBy('source_id')
            ->with('source')
            ->get();

        return response()->json(['data' => $data]);
    }

    public function repLeaderboard(Request $request)
    {
        $user = $request->user();
        $query = Lead::visibleTo($user)->where('status', 'won')
            ->select('owner_id', \DB::raw('COUNT(*) as deals'), \DB::raw('SUM(value) as revenue'))
            ->groupBy('owner_id')
            ->with('owner')
            ->orderByDesc('revenue');

        // non-admins should not see other teams' reps; visibleTo already scopes leads
        $data = $query->get();

        return response()->json(['data' => $data]);
    }

    public function targetProgress(Request $request)
    {
        $user = $request->user();
        $targets = Target::visibleTo($user)->get();

        return response()->json(['data' => $targets]);
    }

    public function tasksOverdue(Request $request)
    {
        $user = $request->user();
        $tasks = Task::visibleTo($user)->where('assignee_id', $user->id)->where('status', 'overdue')->get();

        return response()->json(['data' => $tasks]);
    }
}
