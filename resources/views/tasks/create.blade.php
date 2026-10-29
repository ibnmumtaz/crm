@extends('layouts.app')

@section('content')
    <div class="bg-figma-soft border border-figma-hairline rounded-md p-6 max-w-2xl">
        <h2 class="text-2xl font-bold text-figma-ink mb-6">Create Task</h2>

        <form method="POST" action="{{ route('tasks.store') }}">
            @csrf

            <div class="mb-4">
                <label for="title" class="block font-bold text-sm text-figma-ink mb-1">Title *</label>
                <input id="title" name="title" value="{{ old('title') }}" class="block w-full border border-figma-hairline focus:border-figma-hairline focus:ring-primary rounded-full px-4 py-2 text-gray-600" required />
                @error('title') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="mb-4">
                <label for="lead_id" class="block font-bold text-sm text-figma-ink mb-1">Lead</label>
                <select id="lead_id" name="lead_id" class="block w-full border border-figma-hairline focus:border-figma-hairline focus:ring-primary rounded-md px-4 py-2 text-gray-600">
                    <option value="">-- None --</option>
                    @foreach($leads as $l)
                        <option value="{{ $l->id }}" @selected(old('lead_id') == $l->id)>{{ $l->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="priority" class="block font-bold text-sm text-figma-ink mb-1">Priority</label>
                <select id="priority" name="priority" class="block w-full border border-figma-hairline focus:border-figma-hairline focus:ring-primary rounded-md px-4 py-2 text-gray-600">
                    @foreach(\App\Enums\TaskPriority::cases() as $p)
                        <option value="{{ $p->value }}" @selected(old('priority', 'medium') === $p->value)>{{ ucfirst($p->value) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="due_at" class="block font-bold text-sm text-figma-ink mb-1">Due Date *</label>
                <input id="due_at" name="due_at" type="datetime-local" value="{{ old('due_at') }}" class="block w-full border border-figma-hairline focus:border-figma-hairline focus:ring-primary rounded-full px-4 py-2 text-gray-600" required />
                @error('due_at') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="mb-4">
                <label for="notes" class="block font-bold text-sm text-figma-ink mb-1">Notes</label>
                <textarea id="notes" name="notes" rows="3" class="block w-full border border-figma-hairline focus:border-figma-hairline focus:ring-primary rounded-md px-4 py-2 text-gray-600">{{ old('notes') }}</textarea>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="bg-figma-ink text-figma-canvas px-6 py-2 rounded-full font-bold text-sm hover:bg-figma-ink transition">Create Task</button>
                <a href="{{ route('tasks.index') }}" class="bg-figma-soft border border-figma-hairline text-gray-600 px-6 py-2 rounded-full font-bold text-sm hover:bg-figma-soft transition">Cancel</a>
            </div>
        </form>
    </div>
@endsection
