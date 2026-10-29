@extends('layouts.app')

@section('content')
    <div class="bg-figma-soft border border-figma-hairline rounded-md p-6 max-w-2xl">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-figma-ink">{{ $task->title }}</h2>
            <div class="flex gap-2">
                <a href="{{ route('tasks.edit', $task) }}" class="bg-figma-soft border border-figma-hairline text-figma-ink px-4 py-2 rounded-full font-bold text-sm hover:bg-figma-soft transition">Edit</a>
                <a href="{{ route('tasks.index') }}" class="bg-figma-soft border border-figma-hairline text-gray-600 px-4 py-2 rounded-full font-bold text-sm hover:bg-figma-soft transition">Back</a>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-6">
            <div><span class="font-bold text-gray-600 text-sm">Priority:</span> <span class="text-figma-ink">{{ $task->priority }}</span></div>
            <div><span class="font-bold text-gray-600 text-sm">Status:</span> <span class="text-figma-ink">{{ $task->status }}</span></div>
            <div><span class="font-bold text-gray-600 text-sm">Due:</span> <span class="text-figma-ink">{{ $task->due_at->format('M d, Y H:i') }}</span></div>
            <div><span class="font-bold text-gray-600 text-sm">Assignee:</span> <span class="text-figma-ink">{{ $task->assignee->name ?? '—' }}</span></div>
            @if($task->lead)
                <div><span class="font-bold text-gray-600 text-sm">Lead:</span> <a href="{{ route('leads.show', $task->lead) }}" class="text-figma-ink">{{ $task->lead->name }}</a></div>
            @endif
        </div>

        @if($task->notes)
            <div class="mb-4">
                <span class="font-bold text-gray-600 text-sm">Notes:</span>
                <p class="text-figma-ink mt-1">{{ $task->notes }}</p>
            </div>
        @endif

        @if($task->outcome)
            <div class="mb-4">
                <span class="font-bold text-gray-600 text-sm">Outcome:</span>
                <p class="text-figma-ink mt-1">{{ $task->outcome }}</p>
            </div>
        @endif

        <div class="flex gap-2 mt-6">
            <form method="POST" action="{{ route('tasks.complete', $task) }}" class="inline">
                @csrf
                <button type="submit" class="bg-figma-ink text-figma-canvas px-4 py-2 rounded-full font-bold text-sm hover:bg-figma-ink transition">Complete</button>
            </form>
            <form method="POST" action="{{ route('tasks.cancel', $task) }}" class="inline">
                @csrf
                <button type="submit" class="bg-figma-soft border border-figma-hairline text-gray-600 px-4 py-2 rounded-full font-bold text-sm hover:bg-figma-soft transition">Cancel Task</button>
            </form>
        </div>
    </div>
@endsection
