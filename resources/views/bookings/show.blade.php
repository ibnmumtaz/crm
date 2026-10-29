@extends('layouts.marketing')
@section('content')
    <section class="max-w-5xl mx-auto px-4 py-12 sm:py-16">
        <a href="{{ route('bookings.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-black mb-8">← All meeting types</a>
        @if(session('error'))<div role="alert" class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-red-800">{{ session('error') }}</div>@endif
        @if($errors->any())<div role="alert" class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-red-800">Please correct the highlighted fields and try again.</div>@endif
        <p class="figma-eyebrow mb-3">BOOK A DEMO · STEP 02</p>
        <h1 class="text-4xl sm:text-5xl font-bold text-figma-ink mb-3">{{ $meetingType->name }}</h1>
        <p class="text-figma-ink mb-6">{{ $meetingType->description }} ({{ $meetingType->duration_minutes }} min)</p>
        <form method="POST" action="{{ route('bookings.store') }}" class="grid lg:grid-cols-[1fr_0.9fr] gap-8 mt-8">
            @csrf
            <input type="hidden" name="meeting_type_id" value="{{ $meetingType->id }}" />
            <div class="figma-card space-y-4">
                <h2 class="text-xl font-bold">Your details</h2>
                <div><label for="booking-name" class="block text-figma-ink mb-1">Name *</label><span class="sr-only">Name is required</span><input id="booking-name" autocomplete="name" name="name" value="{{ old('name') }}" class="block w-full border border-figma-hairline rounded-full px-4 py-2 text-figma-ink" required /></div>
                <div><label for="booking-email" class="block text-figma-ink mb-1">Email *</label><input id="booking-email" autocomplete="email" name="email" type="email" value="{{ old('email') }}" class="block w-full border border-figma-hairline rounded-full px-4 py-2 text-figma-ink" required /></div>
                <div><label for="booking-company" class="block text-figma-ink mb-1">Company</label><input id="booking-company" autocomplete="organization" name="company" value="{{ old('company') }}" class="block w-full border border-figma-hairline rounded-full px-4 py-2 text-figma-ink" /></div>
                <div><label for="booking-notes" class="block text-figma-ink mb-1">Anything we should know? (optional)</label><textarea id="booking-notes" name="notes" rows="3" class="block w-full border border-figma-hairline rounded-md px-4 py-2 text-figma-ink">{{ old('notes') }}</textarea></div>
                <div><label for="booking-timezone" class="block text-figma-ink mb-1">Your time zone</label><input id="booking-timezone" name="timezone" value="{{ old('timezone', config('app.timezone', 'UTC')) }}" class="block w-full border border-figma-hairline rounded-full px-4 py-2 text-figma-ink" /></div>
            </div>
            <div class="figma-card">
                <h2 class="text-xl font-bold mb-2">Choose a time</h2>
                <label class="block text-figma-ink mb-2">Available slots · next 2 weeks *</label>
                <div class="max-h-96 overflow-y-auto space-y-2">
                    @foreach($slots as $slot)
                        <label class="flex items-center gap-2 bg-figma-soft border border-figma-hairline rounded p-2 cursor-pointer">
                            <input type="radio" name="starts_at" value="{{ $slot['start']->toIso8601String() }}" required />
                            <span class="text-figma-ink text-sm">{{ $slot['label'] }}</span>
                        </label>
                    @endforeach
                </div>
                @error('starts_at') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
                <button type="submit" class="figma-button mt-4 w-full">Confirm demo booking <span aria-hidden="true">→</span></button>
                <p class="text-xs text-gray-500 mt-3">Your booking is confirmed only after the server verifies the selected slot.</p>
            </div>
        </form>
    </section>
@endsection
