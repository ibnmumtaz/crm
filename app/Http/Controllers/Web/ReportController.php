<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\Target;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $leads = Lead::visibleTo($user);

        $metrics = [
            'total_leads' => (clone $leads)->count(),
            'won_leads' => (clone $leads)->where('status', 'won')->count(),
            'lost_leads' => (clone $leads)->where('status', 'lost')->count(),
            'conversion_rate' => 0,
            'pipeline_value' => (clone $leads)->whereNotIn('status', ['won', 'lost'])->sum('value'),
            'won_value' => (clone $leads)->where('status', 'won')->sum('value'),
            'task_completion' => Task::visibleTo($user)->where('status', 'completed')->count(),
            'overdue_tasks' => Task::visibleTo($user)->where('status', 'overdue')->count(),
        ];

        $closed = $metrics['won_leads'] + $metrics['lost_leads'];
        $metrics['conversion_rate'] = $closed > 0 ? round(($metrics['won_leads'] / $closed) * 100, 2) : 0;

        // lead sources
        $sourceStats = LeadSource::withCount(['leads' => function ($q) use ($user) {
            $q->visibleTo($user);
        }])->get();

        // rep performance
        $repStats = Lead::visibleTo($user)
            ->select('owner_id', \DB::raw('COUNT(*) as total'), \DB::raw('SUM(CASE WHEN status="won" THEN 1 ELSE 0 END) as won'))
            ->groupBy('owner_id')
            ->with('owner')
            ->get();

        $targets = Target::visibleTo($user)->latest()->get();

        return view('reports.index', compact('metrics', 'sourceStats', 'repStats', 'targets'));
    }
}
