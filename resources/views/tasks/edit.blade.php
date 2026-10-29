@extends('layouts.app')

@section('content')
    <div class="bg-figma-soft border border-figma-hairline rounded-md p-6 max-w-2xl">
        <h2 class="text-2xl font-bold text-figma-ink mb-6">Edit Task</h2>

        <form method="POST" action="{{ route('tasks.update', $task) }}">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="title" class="block font-bold text-sm text-figma-ink mb-1">Title *</label>
                <input id="title" name="title" value="{{ old('title', $task->title) }}" class="block w-full border border-figma-hairline focus:border-figma-hairline focus:ring-primary rounded-full px-4 py-2 text-gray-600" required />
            </div>

            <div class="mb-4">
                <label for="priority" class="block font-bold text-sm text-figma-ink mb-1">Priority</label>
                <select id="priority" name="priority" class="block w-full border border-figma-hairline focus:border-figma-hairline focus:ring-primary rounded-md px-4 py-2 text-gray-600">
                    @foreach(\App\Enums\TaskPriority::cases() as $p)
                        <option value="{{ $p->value }}" @selected(old('priority', $task->priority) === $p->value)>{{ ucfirst($p->value) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="status" class="block font-bold text-sm text-figma-ink mb-1">Status</label>
                <select id="status" name="status" class="block w-full border border-figma-hairline focus:border-figma-hairline focus:ring-primary rounded-md px-4 py-2 text-gray-600">
                    @foreach(\App\Enums\TaskStatus::cases() as $s)
                        <option value="{{ $s->value }}" @selected(old('status', $task->status) === $s->value)>{{ ucfirst(str_replace('_', ' ', $s->value)) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="due_at" class="block font-bold text-sm text-figma-ink mb-1">Due Date *</label>
                <input id="due_at" name="due_at" type="datetime-local" value="{{ old('due_at', $task->due_at->format('Y-m-d\TH:i')) }}" class="block w-full border border-figma-hairline focus:border-figma-hairline focus:ring-primary rounded-full px-4 py-2 text-gray-600" required />
            </div>

            <div class="mb-4">
                <label for="notes" class="block font-bold text-sm text-figma-ink mb-1">Notes</label>
                <textarea id="notes" name="notes" rows="3" class="block w-full border border-figma-hairline focus:border-figma-hairline focus:ring-primary rounded-md px-4 py-2 text-gray-600">{{ old('notes', $task->notes) }}</textarea>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="bg-figma-ink text-figma-canvas px-6 py-2 rounded-full font-bold text-sm hover:bg-figma-ink transition">Update Task</button>
                <a href="{{ route('tasks.index') }}" class="bg-figma-soft border border-figma-hairline text-gray-600 px-6 py-2 rounded-full font-bold text-sm hover:bg-figma-soft transition">Cancel</a>
            </div>
        </form>
    </div>
@endsection
