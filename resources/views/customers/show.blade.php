@extends('layouts.app')
@section('content')
    <div class="bg-figma-soft border border-figma-hairline rounded-md p-6 max-w-3xl">
        <h2 class="text-2xl font-bold text-figma-ink mb-2">{{ $customer->name }}</h2>
        <p class="text-gray-600 mb-4">{{ $customer->company }} · {{ $customer->email }} · {{ $customer->phone }}</p>
        @if($customer->lead)
            <p class="text-sm text-gray-600 mb-4">Converted from lead: <a href="{{ route('leads.show', $customer->lead) }}" class="text-figma-ink">{{ $customer->lead->name }}</a></p>
        @endif
        <h3 class="text-lg font-bold text-figma-ink mb-2">Communications</h3>
        <ul class="space-y-2 mb-4">
            @forelse($customer->communications as $comm)
                <li class="bg-figma-soft rounded p-3 text-sm text-figma-ink">{{ $comm->type }} — {{ $comm->subject }} ({{ $comm->occurred_at }})</li>
            @empty
                <li class="text-gray-600 text-sm">No communications.</li>
            @endforelse
        </ul>
        <h3 class="text-lg font-bold text-figma-ink mb-2">Tasks</h3>
        <ul class="space-y-2">
            @forelse($tasks as $task)
                <li class="bg-figma-soft rounded p-3 text-sm text-figma-ink">{{ $task->title }} ({{ $task->status->value ?? $task->status }})</li>
            @empty
                <li class="text-gray-600 text-sm">No tasks.</li>
            @endforelse
        </ul>
    </div>
@endsection
