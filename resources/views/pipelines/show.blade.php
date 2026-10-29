@extends('layouts.app')

@section('content')
    <div class="bg-figma-soft border border-figma-hairline rounded-md p-6 max-w-2xl">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-figma-ink">{{ $pipeline->name }}</h2>
            <a href="{{ route('pipelines.index') }}" class="bg-figma-soft border border-figma-hairline text-gray-600 px-4 py-2 rounded-full font-bold text-sm hover:bg-figma-soft transition">Back</a>
        </div>

        <div class="mb-4">
            <span class="font-bold text-gray-600 text-sm">Default:</span> <span class="text-figma-ink">{{ $pipeline->is_default ? 'Yes' : 'No' }}</span>
        </div>
        <div class="mb-4">
            <span class="font-bold text-gray-600 text-sm">Active:</span> <span class="text-figma-ink">{{ $pipeline->is_active ? 'Yes' : 'No' }}</span>
        </div>

        <h3 class="text-lg font-bold text-figma-ink mb-3">Stages</h3>
        <div class="space-y-2">
            @foreach($pipeline->stages as $stage)
                <div class="border border-figma-hairline rounded-md p-3 bg-figma-soft flex justify-between">
                    <span class="font-bold text-figma-ink">{{ $stage->name }}</span>
                    <span class="text-gray-600 text-sm">{{ $stage->probability }}%</span>
                </div>
            @endforeach
        </div>
    </div>
@endsection
