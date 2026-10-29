@extends('layouts.app')

@section('content')
    <div class="bg-figma-soft border border-figma-hairline rounded-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-figma-ink">Targets</h2>
            <a href="{{ route('targets.create') }}" class="bg-figma-ink text-figma-canvas px-4 py-2 rounded-full font-bold text-sm hover:bg-figma-ink transition">Add Target</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($targets ?? [] as $target)
                <div class="border border-figma-hairline rounded-md p-6 bg-figma-soft">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-figma-ink">{{ ucfirst($target->type) }} Target</h3>
                        <span class="px-2 py-1 text-xs rounded-sm bg-figma-soft text-figma-ink font-bold">{{ ucfirst($target->period) }}</span>
                    </div>
                    <div class="text-3xl font-bold text-figma-ink mb-2">${{ number_format($target->amount, 2) }}</div>
                    <div class="text-sm text-gray-600">
                        {{ $target->period_start->format('M d, Y') }} - {{ $target->period_end->format('M d, Y') }}
                    </div>
                </div>
            @empty
                <p class="text-gray-600 col-span-2">No targets set.</p>
            @endforelse
        </div>
    </div>
@endsection
