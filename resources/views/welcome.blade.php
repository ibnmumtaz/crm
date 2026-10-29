@extends('layouts.guest')

@section('content')
    <div class="text-center">
        <h1 class="text-3xl font-bold text-figma-ink mb-4">CRM System</h1>
        <p class="text-gray-600 mb-6">Sales Management & Customer Relationship Management</p>
        <div class="flex justify-center gap-4">
            <a href="{{ route('login') }}" class="bg-figma-ink text-figma-canvas px-6 py-2 rounded-full font-bold text-sm hover:bg-figma-ink transition">Login</a>
            <a href="{{ route('register') }}" class="bg-figma-soft border border-figma-hairline text-figma-ink px-6 py-2 rounded-full font-bold text-sm hover:bg-figma-soft transition">Register</a>
        </div>
    </div>
@endsection
