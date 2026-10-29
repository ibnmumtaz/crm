@extends('layouts.app')

@section('content')
    <div class="bg-figma-soft border border-figma-hairline rounded-md p-6 max-w-2xl">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-figma-ink">{{ $communication->subject ?? 'Communication' }}</h2>
            <a href="{{ route('communications.index') }}" class="bg-figma-soft border border-figma-hairline text-gray-600 px-4 py-2 rounded-full font-bold text-sm hover:bg-figma-soft transition">Back</a>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-6">
            <div><span class="font-bold text-gray-600 text-sm">Type:</span> <span class="text-figma-ink">{{ $communication->type }}</span></div>
            <div><span class="font-bold text-gray-600 text-sm">Occurred:</span> <span class="text-figma-ink">{{ $communication->occurred_at->format('M d, Y H:i') }}</span></div>
            <div><span class="font-bold text-gray-600 text-sm">Logged by:</span> <span class="text-figma-ink">{{ $communication->user->name ?? '—' }}</span></div>
            @if($communication->lead)
                <div><span class="font-bold text-gray-600 text-sm">Lead:</span> <a href="{{ route('leads.show', $communication->lead) }}" class="text-figma-ink">{{ $communication->lead->name }}</a></div>
            @endif
        </div>

        <div class="mb-4">
            <span class="font-bold text-gray-600 text-sm">Body:</span>
            <p class="text-figma-ink mt-1">{{ $communication->body }}</p>
        </div>

        @if($communication->outcome)
            <div class="mb-4">
                <span class="font-bold text-gray-600 text-sm">Outcome:</span>
                <p class="text-figma-ink mt-1">{{ $communication->outcome }}</p>
            </div>
        @endif
    </div>
@endsection
