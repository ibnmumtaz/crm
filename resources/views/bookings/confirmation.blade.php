@extends('layouts.marketing')
@section('content')
    <section class="max-w-2xl mx-auto px-4 py-16 text-center">
        <h1 class="text-4xl font-bold text-figma-ink mb-4">Booking Confirmed</h1>
        <div class="bg-figma-soft border border-figma-hairline rounded-md p-6 text-left">
            <p class="text-figma-ink mb-2"><strong>{{ $booking->meetingType->name }}</strong></p>
            <p class="text-figma-ink mb-2">{{ $booking->starts_at->format('l, F j, Y g:i A') }} ({{ $booking->timezone }})</p>
            <p class="text-figma-ink mb-2">Name: {{ $booking->name }}</p>
            <p class="text-figma-ink mb-2">Email: {{ $booking->email }}</p>
        </div>
        <form method="POST" action="{{ route('bookings.cancel', $booking) }}" class="mt-6">
            @csrf
            <input type="hidden" name="token" value="{{ $booking->cancellation_token }}" />
            <button type="submit" class="text-red-600 underline text-sm">Cancel booking</button>
        </form>
    </section>
@endsection
