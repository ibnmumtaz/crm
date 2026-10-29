@extends('layouts.app')

@section('content')
    <div class="bg-figma-soft border border-figma-hairline rounded-md p-6">
        <h2 class="text-2xl font-bold text-figma-ink mb-6">Dashboard</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-figma-soft border border-figma-hairline rounded-md p-4">
                <div class="text-gray-600 text-sm font-bold">Total Leads</div>
                <div class="text-3xl font-bold text-figma-ink">{{ $stats['total_leads'] ?? 0 }}</div>
            </div>
            <div class="bg-figma-soft border border-figma-hairline rounded-md p-4">
                <div class="text-gray-600 text-sm font-bold">Won Leads</div>
                <div class="text-3xl font-bold text-figma-ink">{{ $stats['won_leads'] ?? 0 }}</div>
            </div>
            <div class="bg-figma-soft border border-figma-hairline rounded-md p-4">
                <div class="text-gray-600 text-sm font-bold">Pending Tasks</div>
                <div class="text-3xl font-bold text-spotify-orange">{{ $stats['pending_tasks'] ?? 0 }}</div>
            </div>
            <div class="bg-figma-soft border border-figma-hairline rounded-md p-4">
                <div class="text-gray-600 text-sm font-bold">Overdue Tasks</div>
                <div class="text-3xl font-bold text-red-600">{{ $stats['overdue_tasks'] ?? 0 }}</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
            <div class="bg-figma-soft border border-figma-hairline rounded-md p-4">
                <h3 class="text-lg font-bold text-figma-ink mb-4">Pipeline Stage Distribution</h3>
                <canvas id="stageChart" height="200"></canvas>
            </div>
            <div class="bg-figma-soft border border-figma-hairline rounded-md p-4">
                <h3 class="text-lg font-bold text-figma-ink mb-4">Lead Source Performance</h3>
                <canvas id="sourceChart" height="200"></canvas>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div class="bg-figma-soft border border-figma-hairline rounded-md p-4">
                <h3 class="text-lg font-bold text-figma-ink mb-4">Recent Leads</h3>
                <div class="space-y-2">
                    @forelse($recentLeads ?? [] as $lead)
                        <div class="flex justify-between items-center p-3 bg-figma-soft rounded-md border border-figma-hairline">
                            <div>
                                <div class="font-bold text-figma-ink">{{ $lead->name }}</div>
                                <div class="text-sm text-gray-600">{{ $lead->company }}</div>
                            </div>
                            <span class="px-2 py-1 text-xs rounded-sm bg-figma-soft text-figma-ink font-bold">{{ $lead->status }}</span>
                        </div>
                    @empty
                        <p class="text-gray-600">No leads yet.</p>
                    @endforelse
                </div>
                <a href="{{ route('leads.index') }}" class="mt-4 inline-block text-figma-ink hover:text-figma-ink font-bold">View all leads →</a>
            </div>

            <div class="bg-figma-soft border border-figma-hairline rounded-md p-4">
                <h3 class="text-lg font-bold text-figma-ink mb-4">My Tasks</h3>
                <div class="space-y-2">
                    @forelse($myTasks ?? [] as $task)
                        <div class="flex justify-between items-center p-3 bg-figma-soft rounded-md border border-figma-hairline">
                            <div>
                                <div class="font-bold text-figma-ink">{{ $task->title }}</div>
                                <div class="text-sm text-gray-600">Due: {{ $task->due_at->format('M d, Y') }}</div>
                            </div>
                            <span class="px-2 py-1 text-xs rounded-sm bg-warning-surface text-warning-text font-bold">{{ $task->status }}</span>
                        </div>
                    @empty
                        <p class="text-gray-600">No tasks.</p>
                    @endforelse
                </div>
                <a href="{{ route('tasks.index') }}" class="mt-4 inline-block text-figma-ink hover:text-figma-ink font-bold">View all tasks →</a>
            </div>
        </div>
    </div>

    <script>
        const stageCtx = document.getElementById('stageChart');
        if (stageCtx) {
            new Chart(stageCtx, {
                type: 'bar',
                data: {
                    labels: @json($stageLabels ?? []),
                    datasets: [{
                        label: 'Leads',
                        data: @json($stageCounts ?? []),
                        backgroundColor: '#30a83c',
                        borderRadius: 4,
                    }]
                },
                options: { responsive: true, plugins: { legend: { display: false } } }
            });
        }
        const sourceCtx = document.getElementById('sourceChart');
        if (sourceCtx) {
            new Chart(sourceCtx, {
                type: 'doughnut',
                data: {
                    labels: @json($sourceLabels ?? []),
                    datasets: [{
                        data: @json($sourceCounts ?? []),
                        backgroundColor: ['#30a83c', '#ce1736', '#034eb1', '#fb6001', '#7457dd'],
                    }]
                },
                options: { responsive: true }
            });
        }
    </script>
@endsection
