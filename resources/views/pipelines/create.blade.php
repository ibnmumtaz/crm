@extends('layouts.app')

@section('content')
    <div class="bg-figma-soft border border-figma-hairline rounded-md p-6 max-w-2xl">
        <h2 class="text-2xl font-bold text-figma-ink mb-6">Create Pipeline</h2>

        <form method="POST" action="{{ route('pipelines.store') }}">
            @csrf

            <div class="mb-4">
                <label for="name" class="block font-bold text-sm text-figma-ink mb-1">Name *</label>
                <input id="name" name="name" value="{{ old('name') }}" class="block w-full border border-figma-hairline focus:border-figma-hairline focus:ring-primary rounded-full px-4 py-2 text-gray-600" required />
                @error('name') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="flex gap-2">
                <button type="submit" class="bg-figma-ink text-figma-canvas px-6 py-2 rounded-full font-bold text-sm hover:bg-figma-ink transition">Create Pipeline</button>
                <a href="{{ route('pipelines.index') }}" class="bg-figma-soft border border-figma-hairline text-gray-600 px-6 py-2 rounded-full font-bold text-sm hover:bg-figma-soft transition">Cancel</a>
            </div>
        </form>
    </div>
@endsection
