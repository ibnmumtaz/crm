@extends('layouts.app')

@section('content')
    <div class="bg-figma-soft border border-figma-hairline rounded-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-figma-ink">Tasks</h2>
            <a href="{{ route('tasks.create') }}" class="bg-figma-ink text-figma-canvas px-4 py-2 rounded-full font-bold text-sm hover:bg-figma-ink transition">Add Task</a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-hairline">
                <thead class="bg-figma-soft">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Priority</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Due Date</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-figma-soft divide-y divide-hairline">
                    @forelse($tasks ?? [] as $task)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-figma-ink font-bold">{{ $task->title }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($task->priority == 'urgent')
                                    <span class="px-2 py-1 text-xs rounded-sm bg-hot text-figma-ink font-bold">{{ $task->priority }}</span>
                                @elseif($task->priority == 'high')
                                    <span class="px-2 py-1 text-xs rounded-sm bg-warm text-figma-ink font-bold">{{ $task->priority }}</span>
                                @else
                                    <span class="px-2 py-1 text-xs rounded-sm bg-figma-soft text-gray-600 font-bold">{{ $task->priority }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($task->status == 'completed')
                                    <span class="px-2 py-1 text-xs rounded-sm bg-figma-soft text-figma-ink font-bold">{{ $task->status }}</span>
                                @elseif($task->status == 'overdue')
                                    <span class="px-2 py-1 text-xs rounded-sm bg-hot text-figma-ink font-bold">{{ $task->status }}</span>
                                @else
                                    <span class="px-2 py-1 text-xs rounded-sm bg-warning-surface text-warning-text font-bold">{{ $task->status }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ $task->due_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold">
                                <a href="{{ route('tasks.show', $task) }}" class="text-figma-ink hover:text-figma-ink">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-600">No tasks found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
