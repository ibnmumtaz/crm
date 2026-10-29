@extends('layouts.app')

@section('content')
    <div class="bg-figma-soft border border-figma-hairline rounded-md p-6 max-w-2xl">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-figma-ink">{{ ucfirst($target->type) }} Target</h2>
            <a href="{{ route('targets.index') }}" class="bg-figma-soft border border-figma-hairline text-gray-600 px-4 py-2 rounded-full font-bold text-sm hover:bg-figma-soft transition">Back</a>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-6">
            <div><span class="font-bold text-gray-600 text-sm">Type:</span> <span class="text-figma-ink">{{ $target->type }}</span></div>
            <div><span class="font-bold text-gray-600 text-sm">Period:</span> <span class="text-figma-ink">{{ $target->period }}</span></div>
            <div><span class="font-bold text-gray-600 text-sm">Start:</span> <span class="text-figma-ink">{{ $target->period_start->format('M d, Y') }}</span></div>
            <div><span class="font-bold text-gray-600 text-sm">End:</span> <span class="text-figma-ink">{{ $target->period_end->format('M d, Y') }}</span></div>
            <div><span class="font-bold text-gray-600 text-sm">Amount:</span> <span class="text-figma-ink font-bold">${{ number_format($target->amount, 2) }}</span></div>
            <div><span class="font-bold text-gray-600 text-sm">Created by:</span> <span class="text-figma-ink">{{ $target->creator->name ?? '—' }}</span></div>
        </div>
    </div>
@endsection
