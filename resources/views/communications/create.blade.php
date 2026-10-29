@extends('layouts.app')

@section('content')
    <div class="bg-figma-soft border border-figma-hairline rounded-md p-6 max-w-2xl">
        <h2 class="text-2xl font-bold text-figma-ink mb-6">Log Communication</h2>

        <form method="POST" action="{{ route('communications.store') }}">
            @csrf

            <div class="mb-4">
                <label for="lead_id" class="block font-bold text-sm text-figma-ink mb-1">Lead *</label>
                <select id="lead_id" name="lead_id" class="block w-full border border-figma-hairline focus:border-figma-hairline focus:ring-primary rounded-md px-4 py-2 text-gray-600" required>
                    @foreach($leads as $l)
                        <option value="{{ $l->id }}" @selected(old('lead_id') == $l->id)>{{ $l->name }}</option>
                    @endforeach
                </select>
                @error('lead_id') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="mb-4">
                <label for="type" class="block font-bold text-sm text-figma-ink mb-1">Type *</label>
                <select id="type" name="type" class="block w-full border border-figma-hairline focus:border-figma-hairline focus:ring-primary rounded-md px-4 py-2 text-gray-600" required>
                    @foreach(\App\Enums\CommunicationType::cases() as $t)
                        <option value="{{ $t->value }}" @selected(old('type') === $t->value)>{{ ucfirst($t->value) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="subject" class="block font-bold text-sm text-figma-ink mb-1">Subject</label>
                <input id="subject" name="subject" value="{{ old('subject') }}" class="block w-full border border-figma-hairline focus:border-figma-hairline focus:ring-primary rounded-full px-4 py-2 text-gray-600" />
            </div>

            <div class="mb-4">
                <label for="body" class="block font-bold text-sm text-figma-ink mb-1">Body *</label>
                <textarea id="body" name="body" rows="4" class="block w-full border border-figma-hairline focus:border-figma-hairline focus:ring-primary rounded-md px-4 py-2 text-gray-600" required>{{ old('body') }}</textarea>
                @error('body') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="mb-4">
                <label for="outcome" class="block font-bold text-sm text-figma-ink mb-1">Outcome</label>
                <input id="outcome" name="outcome" value="{{ old('outcome') }}" class="block w-full border border-figma-hairline focus:border-figma-hairline focus:ring-primary rounded-full px-4 py-2 text-gray-600" />
            </div>

            <div class="mb-4">
                <label for="occurred_at" class="block font-bold text-sm text-figma-ink mb-1">Occurred At *</label>
                <input id="occurred_at" name="occurred_at" type="datetime-local" value="{{ old('occurred_at', now()->format('Y-m-d\TH:i')) }}" class="block w-full border border-figma-hairline focus:border-figma-hairline focus:ring-primary rounded-full px-4 py-2 text-gray-600" required />
            </div>

            <div class="flex gap-2">
                <button type="submit" class="bg-figma-ink text-figma-canvas px-6 py-2 rounded-full font-bold text-sm hover:bg-figma-ink transition">Log Communication</button>
                <a href="{{ route('communications.index') }}" class="bg-figma-soft border border-figma-hairline text-gray-600 px-6 py-2 rounded-full font-bold text-sm hover:bg-figma-soft transition">Cancel</a>
            </div>
        </form>
    </div>
@endsection
