@extends('layouts.marketing')
@section('content')
    <section class="max-w-7xl mx-auto px-6 py-24">
        <h1 class="text-6xl md:text-7xl font-light tracking-tight leading-none mb-6">Simple pricing for growing teams.</h1>
        <p class="text-xl md:text-2xl font-light max-w-3xl mb-8">Start small, add your team, and upgrade when you need more capacity and support.</p>
    </section>

    <section class="max-w-7xl mx-auto px-6 py-12">
        <div class="grid md:grid-cols-3 gap-6">
            <div class="bg-figma-canvas border border-figma-hairline rounded-lg p-8">
                <h2 class="text-2xl font-bold text-figma-ink mb-2">Starter</h2>
                <p class="text-4xl font-light mb-4">$0</p>
                <p class="text-sm text-neutral-600 mb-6">For individuals getting organized.</p>
                <ul class="space-y-2 text-sm text-neutral-700 mb-6">
                    <li>Lead and task tracking</li>
                    <li>Basic pipeline</li>
                    <li>Email support</li>
                </ul>
                <a href="{{ route('register') }}" class="bg-figma-ink text-figma-canvas px-6 py-3 rounded-pill font-medium inline-block">Get started</a>
            </div>
            <div class="bg-figma-soft border border-figma-hairline rounded-lg p-8">
                <h2 class="text-2xl font-bold text-figma-ink mb-2">Team</h2>
                <p class="text-4xl font-light mb-4">$49</p>
                <p class="text-sm text-neutral-600 mb-6">Per month for growing sales teams.</p>
                <ul class="space-y-2 text-sm text-neutral-700 mb-6">
                    <li>Everything in Starter</li>
                    <li>Team roles and permissions</li>
                    <li>Targets and reports</li>
                    <li>Demo booking</li>
                </ul>
                <a href="{{ route('register') }}" class="bg-figma-ink text-figma-canvas px-6 py-3 rounded-pill font-medium inline-block">Start trial</a>
            </div>
            <div class="bg-figma-navy text-white rounded-lg p-8">
                <h2 class="text-2xl font-bold mb-2">Enterprise</h2>
                <p class="text-4xl font-light mb-4">Custom</p>
                <p class="text-sm text-white/80 mb-6">For larger organizations.</p>
                <ul class="space-y-2 text-sm text-white/90 mb-6">
                    <li>Everything in Team</li>
                    <li>Advanced administration</li>
                    <li>Priority support</li>
                    <li>Custom onboarding</li>
                </ul>
                <a href="{{ route('marketing.contact') }}" class="bg-figma-canvas text-figma-ink px-6 py-3 rounded-pill font-medium inline-block">Contact sales</a>
            </div>
        </div>
        <p class="text-xs text-neutral-500 mt-6">Pricing is illustrative for the demo application.</p>
    </section>
@endsection
