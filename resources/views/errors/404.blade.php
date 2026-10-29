@extends('layouts.marketing')
@section('content')
    <section class="max-w-2xl mx-auto px-4 py-24 text-center">
        <h1 class="text-6xl font-bold text-figma-ink mb-4">404</h1>
        <p class="text-gray-600 mb-6">The page you are looking for could not be found.</p>
        <a href="{{ route('marketing.home') }}" class="bg-figma-ink text-figma-canvas px-6 py-3 rounded-full font-bold">Go home</a>
    </section>
@endsection
