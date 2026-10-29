<?php

namespace App\Http\Controllers\Web;

use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\PipelineStage;
use App\Models\Task;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $user = Auth::user();

        $leads = Lead::visibleTo($user);
        $stats = [
            'total_leads' => (clone $leads)->count(),
            'won_leads' => (clone $leads)->where('status', 'won')->count(),
            'pending_tasks' => Task::visibleTo($user)->where('status', 'pending')->count(),
            'overdue_tasks' => Task::visibleTo($user)->where('status', 'overdue')->count(),
        ];

        $recentLeads = (clone $leads)->latest()->take(5)->get();
        $myTasks = Task::visibleTo($user)->where('assignee_id', $user->id)->whereIn('status', ['pending', 'overdue'])->take(5)->get();

        $pipelineStages = PipelineStage::withCount(['leads' => function ($q) use ($user) {
            $q->visibleTo($user);
        }])->get();
        $stageLabels = $pipelineStages->pluck('name')->toArray();
        $stageCounts = $pipelineStages->pluck('leads_count')->toArray();

        $leadSources = LeadSource::withCount(['leads' => function ($q) use ($user) {
            $q->visibleTo($user);
        }])->get();
        $sourceLabels = $leadSources->pluck('name')->toArray();
        $sourceCounts = $leadSources->pluck('leads_count')->toArray();

        return view('dashboard.index', compact('stats', 'recentLeads', 'myTasks', 'stageLabels', 'stageCounts', 'sourceLabels', 'sourceCounts'));
    }
}
