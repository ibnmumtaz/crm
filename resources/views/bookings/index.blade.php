@extends('layouts.marketing')
@section('content')
    <section class="max-w-4xl mx-auto px-4 py-16">
        <h1 class="text-4xl font-bold text-figma-ink mb-8 text-center">Book a Meeting</h1>
        <div class="grid md:grid-cols-3 gap-4">
            @foreach($meetingTypes as $mt)
                <a href="{{ route('bookings.show', $mt) }}" class="bg-figma-soft border border-figma-hairline rounded-md p-6 hover:border-spotify-green transition">
                    <h3 class="text-figma-ink font-bold mb-2">{{ $mt->name }}</h3>
                    <p class="text-figma-ink text-sm mb-2">{{ $mt->description }}</p>
                    <span class="text-figma-ink text-sm">{{ $mt->duration_minutes }} minutes</span>
                </a>
            @endforeach
        </div>
    </section>
@endsection
