@extends('layouts.app')
@section('content')
    <h2 class="text-2xl font-bold text-figma-ink mb-6">Reports</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        @foreach($metrics as $k => $v)
            <div class="bg-figma-soft border border-figma-hairline rounded-md p-4">
                <div class="text-gray-600 text-xs uppercase">{{ str_replace('_',' ',$k) }}</div>
                <div class="text-figma-ink text-2xl font-bold">{{ $v }}</div>
            </div>
        @endforeach
    </div>
    <div class="bg-figma-soft border border-figma-hairline rounded-md p-4 mb-6">
        <h3 class="text-figma-ink font-bold mb-2">Lead Sources</h3>
        <ul class="text-gray-600 text-sm">
            @foreach($sourceStats as $s)
                <li>{{ $s->name }}: {{ $s->leads_count }}</li>
            @endforeach
        </ul>
    </div>
    <div class="bg-figma-soft border border-figma-hairline rounded-md p-4 mb-6">
        <h3 class="text-figma-ink font-bold mb-2">Rep Performance</h3>
        <table class="w-full text-sm">
            <thead class="text-gray-600"><tr><th class="text-left">Rep</th><th class="text-left">Total</th><th class="text-left">Won</th></tr></thead>
            <tbody>
                @foreach($repStats as $r)
                    <tr class="border-t border-figma-hairline"><td class="text-figma-ink py-2">{{ optional($r->owner)->name }}</td><td class="text-gray-600">{{ $r->total }}</td><td class="text-gray-600">{{ $r->won }}</td></tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="bg-figma-soft border border-figma-hairline rounded-md p-4">
        <h3 class="text-figma-ink font-bold mb-2">Targets</h3>
        <ul class="text-gray-600 text-sm">
            @foreach($targets as $t)
                <li>{{ $t->type->value ?? $t->type }} — {{ $t->amount }} ({{ $t->period_start }} to {{ $t->period_end }})</li>
            @endforeach
        </ul>
    </div>
@endsection
