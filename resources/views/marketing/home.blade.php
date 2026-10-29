@extends('layouts.marketing')
@section('content')
    <section class="max-w-7xl mx-auto px-6 py-24">
        <h1 class="text-6xl md:text-8xl font-light tracking-tight leading-none mb-6">A CRM that keeps every lead moving.</h1>
        <p class="text-xl md:text-2xl font-light max-w-3xl mb-8">Track leads, conversations, tasks, and targets in one secure workflow. Built for sales teams that need clarity, accountability, and momentum.</p>
        <div class="flex flex-wrap gap-4">
            <a href="{{ route('register') }}" class="bg-figma-ink text-figma-canvas px-6 py-3 rounded-pill font-medium">Get started for free</a>
            <a href="{{ route('bookings.index') }}" class="bg-figma-canvas text-figma-ink px-6 py-3 rounded-pill font-medium border border-figma-hairline">Book a demo</a>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-6 pb-16">
        <div class="rounded-2xl border border-figma-hairline bg-figma-soft p-4 md:p-7 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-figma-hairline pb-4 mb-5">
                <div><p class="font-mono text-xs uppercase tracking-widest text-neutral-500">Workspace preview</p><h2 class="text-xl md:text-2xl font-semibold mt-1">Your sales, in focus.</h2></div>
                <span class="inline-flex items-center gap-2 rounded-full bg-figma-mint px-3 py-1.5 text-xs font-semibold"><span class="h-2 w-2 rounded-full bg-green-700"></span> Workflow overview</span>
            </div>
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <div class="rounded-xl bg-white border border-figma-hairline p-5"><p class="text-sm text-neutral-500">Pipeline value</p><p class="text-3xl font-semibold mt-3">$248,500</p><p class="text-xs mt-2 text-neutral-500">Sample preview data</p></div>
                <div class="rounded-xl bg-figma-lime p-5"><p class="text-sm">Active opportunities</p><p class="text-3xl font-semibold mt-3">36</p><p class="text-xs mt-2">Sample preview data</p></div>
                <div class="rounded-xl bg-figma-lilac p-5"><p class="text-sm">Follow-ups due</p><p class="text-3xl font-semibold mt-3">12</p><p class="text-xs mt-2">Sample preview data</p></div>
                <div class="rounded-xl bg-figma-cream p-5"><p class="text-sm">Target progress</p><p class="text-3xl font-semibold mt-3">72%</p><div class="h-2 rounded-full bg-white/80 mt-3 overflow-hidden"><div class="h-full w-[72%] rounded-full bg-black"></div></div><p class="text-xs mt-2">Sample preview data</p></div>
            </div>
            <p class="text-xs text-neutral-500 mt-4">Illustrative marketing preview only. Sign in to view data from your own workspace.</p>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-6 py-6">
        <div class="bg-figma-lime rounded-lg p-12">
            <h2 class="text-3xl font-medium mb-4">Pipeline that stays honest.</h2>
            <p class="text-lg max-w-2xl">Every stage change is recorded. Every owner is visible. Every value is scoped to your team.</p>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-6 py-6">
        <div class="bg-figma-navy text-white rounded-lg p-12">
            <h2 class="text-3xl font-medium mb-4">Ship better sales processes.</h2>
            <p class="text-lg max-w-2xl">From first touch to closed deal, your workflow lives in one place.</p>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-6 py-6">
        <div class="bg-figma-coral rounded-lg p-12">
            <h2 class="text-3xl font-medium mb-4">Follow-ups that never slip.</h2>
            <p class="text-lg max-w-2xl">Tasks, reminders, and overdue indicators keep reps on track.</p>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-6 py-24">
        <h2 class="text-4xl font-medium mb-8">Everything your sales team needs</h2>
        <div class="grid md:grid-cols-3 gap-6">
            @foreach([
                ['Lead management','Capture, qualify, assign, and track leads through every stage.'],
                ['Pipeline board','Visual Kanban with drag-and-drop stage changes.'],
                ['Task follow-ups','Never miss a next step with due dates and reminders.'],
                ['Communications','Log calls, emails, meetings, and notes on every record.'],
                ['Targets','Set revenue and deal-count goals and track progress.'],
                ['Reports','Pipeline value, conversion, sources, and team performance.'],
            ] as $f)
                <div class="bg-figma-soft rounded-md p-6">
                    <h3 class="font-bold mb-2">{{ $f[0] }}</h3>
                    <p class="text-sm">{{ $f[1] }}</p>
                </div>
            @endforeach
        </div>
    </section>
@endsection
