@extends('layouts.app')

@section('content')
    <div class="bg-figma-soft border border-figma-hairline rounded-md p-6 max-w-2xl">
        <h2 class="text-2xl font-bold text-figma-ink mb-6">Create Lead</h2>

        <form method="POST" action="{{ route('leads.store') }}">
            @csrf

            <div class="mb-4">
                <label for="name" class="block font-bold text-sm text-figma-ink mb-1">Name *</label>
                <input id="name" name="name" value="{{ old('name') }}" class="block w-full border border-figma-hairline focus:border-figma-hairline focus:ring-primary rounded-full px-4 py-2 text-gray-600" required />
                @error('name') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="mb-4">
                <label for="company" class="block font-bold text-sm text-figma-ink mb-1">Company</label>
                <input id="company" name="company" value="{{ old('company') }}" class="block w-full border border-figma-hairline focus:border-figma-hairline focus:ring-primary rounded-full px-4 py-2 text-gray-600" />
            </div>

            <div class="mb-4">
                <label for="email" class="block font-bold text-sm text-figma-ink mb-1">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" class="block w-full border border-figma-hairline focus:border-figma-hairline focus:ring-primary rounded-full px-4 py-2 text-gray-600" />
                @error('email') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="mb-4">
                <label for="phone" class="block font-bold text-sm text-figma-ink mb-1">Phone</label>
                <input id="phone" name="phone" value="{{ old('phone') }}" class="block w-full border border-figma-hairline focus:border-figma-hairline focus:ring-primary rounded-full px-4 py-2 text-gray-600" />
            </div>

            <div class="mb-4">
                <label for="value" class="block font-bold text-sm text-figma-ink mb-1">Value</label>
                <input id="value" name="value" type="number" step="0.01" value="{{ old('value') }}" class="block w-full border border-figma-hairline focus:border-figma-hairline focus:ring-primary rounded-full px-4 py-2 text-gray-600" />
            </div>

            <div class="mb-4">
                <label for="pipeline_id" class="block font-bold text-sm text-figma-ink mb-1">Pipeline *</label>
                <select id="pipeline_id" name="pipeline_id" class="block w-full border border-figma-hairline focus:border-figma-hairline focus:ring-primary rounded-md px-4 py-2 text-gray-600" required>
                    @foreach(\App\Models\Pipeline::all() as $p)
                        <option value="{{ $p->id }}" @selected(old('pipeline_id') == $p->id)>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="stage_id" class="block font-bold text-sm text-figma-ink mb-1">Stage *</label>
                <select id="stage_id" name="stage_id" class="block w-full border border-figma-hairline focus:border-figma-hairline focus:ring-primary rounded-md px-4 py-2 text-gray-600" required>
                    @foreach(\App\Models\PipelineStage::all() as $s)
                        <option value="{{ $s->id }}" @selected(old('stage_id') == $s->id)>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="source_id" class="block font-bold text-sm text-figma-ink mb-1">Source</label>
                <select id="source_id" name="source_id" class="block w-full border border-figma-hairline focus:border-figma-hairline focus:ring-primary rounded-md px-4 py-2 text-gray-600">
                    <option value="">-- None --</option>
                    @foreach(\App\Models\LeadSource::all() as $s)
                        <option value="{{ $s->id }}" @selected(old('source_id') == $s->id)>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="expected_close" class="block font-bold text-sm text-figma-ink mb-1">Expected Close</label>
                <input id="expected_close" name="expected_close" type="date" value="{{ old('expected_close') }}" class="block w-full border border-figma-hairline focus:border-figma-hairline focus:ring-primary rounded-full px-4 py-2 text-gray-600" />
            </div>

            <div class="flex gap-2">
                <button type="submit" class="bg-figma-ink text-figma-canvas px-6 py-2 rounded-full font-bold text-sm hover:bg-figma-ink transition">Create Lead</button>
                <a href="{{ route('leads.index') }}" class="bg-figma-soft border border-figma-hairline text-gray-600 px-6 py-2 rounded-full font-bold text-sm hover:bg-figma-soft transition">Cancel</a>
            </div>
        </form>
    </div>
@endsection
