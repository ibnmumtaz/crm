@extends('layouts.marketing')
@section('content')
    <section class="max-w-7xl mx-auto px-6 py-24">
        <h1 class="text-6xl md:text-7xl font-light tracking-tight leading-none mb-6">Features that keep deals moving.</h1>
        <p class="text-xl md:text-2xl font-light max-w-3xl mb-8">A complete sales workspace for leads, contacts, tasks, conversations, pipelines, targets, and reporting.</p>
        <div class="flex flex-wrap gap-4">
            <a href="{{ route('register') }}" class="bg-figma-ink text-figma-canvas px-6 py-3 rounded-pill font-medium">Start free</a>
            <a href="{{ route('bookings.index') }}" class="bg-figma-canvas text-figma-ink px-6 py-3 rounded-pill font-medium border border-figma-hairline">Book a demo</a>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-6 py-12">
        <div class="grid md:grid-cols-3 gap-6">
            @foreach([
                ['Lead management', 'Capture, qualify, assign, and track leads through every stage.'],
                ['Pipeline board', 'Visual Kanban with drag-and-drop stage changes and stage history.'],
                ['Task follow-ups', 'Due dates, priorities, completion, and overdue indicators.'],
                ['Communications', 'Log calls, emails, meetings, and notes on every record.'],
                ['Customers', 'Convert won leads and keep contact and interaction history.'],
                ['Targets', 'Set revenue and deal-count goals and track progress.'],
                ['Reports', 'Pipeline value, conversion, lead sources, and team performance.'],
                ['Team administration', 'Users, roles, permissions, and team assignments.'],
                ['Demo booking', 'Public meeting scheduling with conflict prevention.'],
            ] as $feature)
                <div class="bg-figma-soft rounded-md p-6">
                    <h3 class="font-bold mb-2 text-figma-ink">{{ $feature[0] }}</h3>
                    <p class="text-sm text-neutral-700">{{ $feature[1] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-6 py-16">
        <div class="grid md:grid-cols-2 gap-6">
            <div class="bg-figma-lime rounded-lg p-10">
                <h2 class="text-3xl font-medium mb-4">Security built in.</h2>
                <p class="text-lg">Role-based access, scoped records, CSRF protection, server-side validation, and audit-friendly workflows.</p>
            </div>
            <div class="bg-figma-lilac rounded-lg p-10">
                <h2 class="text-3xl font-medium mb-4">Ready for your team.</h2>
                <p class="text-lg">Responsive layouts, clear empty states, and a consistent design system across every module.</p>
            </div>
        </div>
    </section>
@endsection
