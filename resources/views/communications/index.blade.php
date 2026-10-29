@extends('layouts.app')

@section('content')
    <div class="bg-figma-soft border border-figma-hairline rounded-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-figma-ink">Communications</h2>
            <a href="{{ route('communications.create') }}" class="bg-figma-ink text-figma-canvas px-4 py-2 rounded-full font-bold text-sm hover:bg-figma-ink transition">Add Communication</a>
        </div>

        <div class="space-y-3">
            @forelse($communications ?? [] as $communication)
                <div class="border border-figma-hairline rounded-md p-4 bg-figma-soft">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <span class="px-2 py-1 text-xs rounded-sm bg-figma-soft text-gray-600 font-bold">{{ $communication->type }}</span>
                            <h3 class="text-lg font-bold text-figma-ink mt-1">{{ $communication->subject }}</h3>
                        </div>
                        <div class="text-sm text-gray-600">{{ $communication->occurred_at->format('M d, Y H:i') }}</div>
                    </div>
                    <p class="text-gray-600">{{ $communication->body }}</p>
                    @if($communication->outcome)
                        <p class="mt-2 text-sm text-gray-600"><strong class="text-figma-ink">Outcome:</strong> {{ $communication->outcome }}</p>
                    @endif
                </div>
            @empty
                <p class="text-gray-600">No communications found.</p>
            @endforelse
        </div>
    </div>
@endsection
