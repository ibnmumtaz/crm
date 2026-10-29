@extends('layouts.app')

@section('content')
    <div class="bg-figma-soft border border-figma-hairline rounded-md p-6 max-w-2xl">
        <h2 class="text-2xl font-bold text-figma-ink mb-6">Edit Lead</h2>

        <form method="POST" action="{{ route('leads.update', $lead) }}">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="name" class="block font-bold text-sm text-figma-ink mb-1">Name *</label>
                <input id="name" name="name" value="{{ old('name', $lead->name) }}" class="block w-full border border-figma-hairline focus:border-figma-hairline focus:ring-primary rounded-full px-4 py-2 text-gray-600" required />
                @error('name') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="mb-4">
                <label for="company" class="block font-bold text-sm text-figma-ink mb-1">Company</label>
                <input id="company" name="company" value="{{ old('company', $lead->company) }}" class="block w-full border border-figma-hairline focus:border-figma-hairline focus:ring-primary rounded-full px-4 py-2 text-gray-600" />
            </div>

            <div class="mb-4">
                <label for="email" class="block font-bold text-sm text-figma-ink mb-1">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email', $lead->email) }}" class="block w-full border border-figma-hairline focus:border-figma-hairline focus:ring-primary rounded-full px-4 py-2 text-gray-600" />
            </div>

            <div class="mb-4">
                <label for="phone" class="block font-bold text-sm text-figma-ink mb-1">Phone</label>
                <input id="phone" name="phone" value="{{ old('phone', $lead->phone) }}" class="block w-full border border-figma-hairline focus:border-figma-hairline focus:ring-primary rounded-full px-4 py-2 text-gray-600" />
            </div>

            <div class="mb-4">
                <label for="value" class="block font-bold text-sm text-figma-ink mb-1">Value</label>
                <input id="value" name="value" type="number" step="0.01" value="{{ old('value', $lead->value) }}" class="block w-full border border-figma-hairline focus:border-figma-hairline focus:ring-primary rounded-full px-4 py-2 text-gray-600" />
            </div>

            <div class="mb-4">
                <label for="status" class="block font-bold text-sm text-figma-ink mb-1">Status</label>
                <select id="status" name="status" class="block w-full border border-figma-hairline focus:border-figma-hairline focus:ring-primary rounded-md px-4 py-2 text-gray-600">
                    @foreach(\App\Enums\LeadStatus::cases() as $status)
                        <option value="{{ $status->value }}" @selected(old('status', $lead->status?->value) === $status->value)>{{ $status->label() }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="bg-figma-ink text-figma-canvas px-6 py-2 rounded-full font-bold text-sm hover:bg-figma-ink transition">Update Lead</button>
                <a href="{{ route('leads.index') }}" class="bg-figma-soft border border-figma-hairline text-gray-600 px-6 py-2 rounded-full font-bold text-sm hover:bg-figma-soft transition">Cancel</a>
            </div>
        </form>
    </div>
@endsection
