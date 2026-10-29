@extends('layouts.app')

@section('content')
    <div class="bg-figma-soft border border-figma-hairline rounded-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-figma-ink">Pipelines</h2>
            <a href="{{ route('pipelines.create') }}" class="bg-figma-ink text-figma-canvas px-4 py-2 rounded-full font-bold text-sm hover:bg-figma-ink transition">Add Pipeline</a>
        </div>

        <div class="space-y-4">
            @forelse($pipelines ?? [] as $pipeline)
                <div class="border border-figma-hairline rounded-md p-6 bg-figma-soft">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-figma-ink">{{ $pipeline->name }}</h3>
                        <span class="px-2 py-1 text-xs rounded-sm {{ $pipeline->is_default ? 'bg-figma-soft text-figma-ink' : 'bg-figma-soft text-gray-600' }} font-bold">
                            {{ $pipeline->is_default ? 'Default' : 'Active' }}
                        </span>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @foreach($pipeline->stages as $stage)
                            <div class="px-3 py-2 bg-figma-soft rounded-md text-sm border border-figma-hairline">
                                <span class="font-bold text-figma-ink">{{ $stage->name }}</span>
                                <span class="text-gray-600">({{ $stage->probability }}%)</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <p class="text-gray-600">No pipelines found.</p>
            @endforelse
        </div>
    </div>
@endsection
