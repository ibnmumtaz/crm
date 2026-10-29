@extends('layouts.marketing')
@section('content')
    <section class="max-w-7xl mx-auto px-6 py-24">
        <h1 class="text-6xl md:text-7xl font-light tracking-tight leading-none mb-6">Solutions for every sales role.</h1>
        <p class="text-xl md:text-2xl font-light max-w-3xl mb-8">Give reps focus, managers visibility, admins control, and analysts trusted reporting.</p>
    </section>

    <section class="max-w-7xl mx-auto px-6 py-12">
        <div class="grid md:grid-cols-2 gap-6">
            <div class="bg-figma-soft rounded-md p-6">
                <h2 class="text-2xl font-bold text-figma-ink mb-3">Sales representatives</h2>
                <p class="text-sm text-neutral-700">My leads, tasks, follow-ups, and personal targets in one focused workspace.</p>
            </div>
            <div class="bg-figma-soft rounded-md p-6">
                <h2 class="text-2xl font-bold text-figma-ink mb-3">Sales managers</h2>
                <p class="text-sm text-neutral-700">Team pipeline value, rep performance, assignments, and coaching visibility.</p>
            </div>
            <div class="bg-figma-soft rounded-md p-6">
                <h2 class="text-2xl font-bold text-figma-ink mb-3">Administrators</h2>
                <p class="text-sm text-neutral-700">Users, roles, teams, pipelines, lead sources, settings, and bookings.</p>
            </div>
            <div class="bg-figma-soft rounded-md p-6">
                <h2 class="text-2xl font-bold text-figma-ink mb-3">Analysts</h2>
                <p class="text-sm text-neutral-700">Read-only reporting on conversion, pipeline value, sources, and team results.</p>
            </div>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-6 py-16">
        <div class="bg-figma-cream rounded-lg p-12">
            <h2 class="text-3xl font-medium mb-4">Built for real sales operations.</h2>
            <p class="text-lg max-w-2xl">From first contact to closed deal, every activity, owner, stage, and target stays connected.</p>
            <div class="mt-6 flex flex-wrap gap-4">
                <a href="{{ route('marketing.features') }}" class="bg-figma-ink text-figma-canvas px-6 py-3 rounded-pill font-medium">Explore features</a>
                <a href="{{ route('bookings.index') }}" class="bg-figma-canvas text-figma-ink px-6 py-3 rounded-pill font-medium border border-figma-hairline">Book a demo</a>
            </div>
        </div>
    </section>
@endsection
