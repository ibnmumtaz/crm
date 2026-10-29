@extends('layouts.app')

@section('content')
    <div class="bg-figma-soft border border-figma-hairline rounded-md p-6 max-w-3xl">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-figma-ink">{{ $lead->name }}</h2>
            <div class="flex gap-2">
                @if($lead->status->value === 'won')
                    <form method="POST" action="{{ route('leads.convert', $lead) }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-figma-ink text-black px-4 py-2 rounded-pill font-bold text-sm hover:opacity-90 transition">Convert to Customer</button>
                    </form>
                @endif
                <a href="{{ route('leads.edit', $lead) }}" class="bg-figma-soft border border-figma-hairline text-figma-ink px-4 py-2 rounded-full font-bold text-sm hover:bg-figma-soft transition">Edit</a>
                <a href="{{ route('leads.index') }}" class="bg-figma-soft border border-figma-hairline text-gray-600 px-4 py-2 rounded-full font-bold text-sm hover:bg-figma-soft transition">Back</a>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-6">
            <div><span class="font-bold text-gray-600 text-sm">Company:</span> <span class="text-figma-ink">{{ $lead->company ?? '—' }}</span></div>
            <div><span class="font-bold text-gray-600 text-sm">Email:</span> <span class="text-figma-ink">{{ $lead->email ?? '—' }}</span></div>
            <div><span class="font-bold text-gray-600 text-sm">Phone:</span> <span class="text-figma-ink">{{ $lead->phone ?? '—' }}</span></div>
            <div><span class="font-bold text-gray-600 text-sm">Value:</span> <span class="text-figma-ink font-bold">${{ number_format($lead->value, 2) }}</span></div>
            <div><span class="font-bold text-gray-600 text-sm">Status:</span> <span class="px-2 py-1 text-xs rounded-sm bg-figma-soft text-figma-ink font-bold">{{ $lead->status }}</span></div>
            <div><span class="font-bold text-gray-600 text-sm">Stage:</span> <span class="text-figma-ink">{{ $lead->stage->name ?? '—' }}</span></div>
            <div><span class="font-bold text-gray-600 text-sm">Owner:</span> <span class="text-figma-ink">{{ $lead->owner->name ?? '—' }}</span></div>
            <div><span class="font-bold text-gray-600 text-sm">Expected Close:</span> <span class="text-figma-ink">{{ $lead->expected_close?->format('M d, Y') ?? '—' }}</span></div>
        </div>

        <h3 class="text-lg font-bold text-figma-ink mb-3">Stage History</h3>
        <div class="space-y-2 mb-6">
            @forelse($lead->stageHistory as $h)
                <div class="border border-figma-hairline rounded-md p-3 bg-figma-soft text-sm">
                    <span class="text-gray-600">{{ $h->created_at->format('M d, Y H:i') }}</span>
                    — <span class="text-figma-ink font-bold">{{ $h->fromStage->name ?? '—' }}</span> → <span class="text-figma-ink font-bold">{{ $h->toStage->name }}</span>
                    @if($h->reason) <span class="text-gray-600">({{ $h->reason }})</span> @endif
                </div>
            @empty
                <p class="text-gray-600 text-sm">No stage history.</p>
            @endforelse
        </div>

        <h3 class="text-lg font-bold text-figma-ink mb-3">Communications</h3>
        <div class="space-y-2">
            @forelse($lead->communications as $c)
                <div class="border border-figma-hairline rounded-md p-3 bg-figma-soft">
                    <div class="flex justify-between">
                        <span class="px-2 py-1 text-xs rounded-sm bg-figma-soft text-gray-600 font-bold border border-figma-hairline">{{ $c->type }}</span>
                        <span class="text-xs text-gray-600">{{ $c->occurred_at->format('M d, Y H:i') }}</span>
                    </div>
                    <div class="font-bold text-figma-ink mt-1">{{ $c->subject }}</div>
                    <p class="text-gray-600 text-sm">{{ $c->body }}</p>
                </div>
            @empty
                <p class="text-gray-600 text-sm">No communications.</p>
            @endforelse
        </div>
    </div>
@endsection
