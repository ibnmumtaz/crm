@extends('layouts.marketing')

@section('content')
    <section class="max-w-2xl mx-auto px-4 py-16">
        <h1 class="text-4xl font-bold text-figma-ink mb-8 text-center">Contact Us</h1>
        @if (session('success'))
            <div class="mb-4 text-green-400">{{ session('success') }}</div>
        @endif
        <div class="bg-figma-soft border border-figma-hairline rounded-md p-6">
            <form method="POST" action="{{ route('marketing.contact.submit') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block font-bold text-sm text-figma-ink mb-1">Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="block w-full border border-figma-hairline focus:border-figma-hairline focus:ring-primary rounded-full px-4 py-2 text-figma-ink" required />
                    @error('name') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="block font-bold text-sm text-figma-ink mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="block w-full border border-figma-hairline focus:border-figma-hairline focus:ring-primary rounded-full px-4 py-2 text-figma-ink" required />
                    @error('email') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="block font-bold text-sm text-figma-ink mb-1">Subject</label>
                    <input type="text" name="subject" value="{{ old('subject') }}" class="block w-full border border-figma-hairline focus:border-figma-hairline focus:ring-primary rounded-full px-4 py-2 text-figma-ink" />
                </div>
                <div>
                    <label class="block font-bold text-sm text-figma-ink mb-1">Message</label>
                    <textarea name="message" rows="4" class="block w-full border border-figma-hairline focus:border-figma-hairline focus:ring-primary rounded-md px-4 py-2 text-figma-ink" required>{{ old('message') }}</textarea>
                    @error('message') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
                </div>
                <button type="submit" class="bg-figma-ink text-figma-canvas px-6 py-2 rounded-full font-bold text-sm hover:bg-figma-ink transition">Send Message</button>
            </form>
        </div>
    </section>
@endsection
