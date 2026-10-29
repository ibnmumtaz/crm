@extends('layouts.app')
@section('content')
<div class="figma-card max-w-2xl">
    <h1 class="text-2xl font-bold mb-4">{{ $user->name }}</h1>
    <div class="space-y-2 text-sm">
        <div><span class="figma-eyebrow">Email</span><div>{{ $user->email }}</div></div>
        <div><span class="figma-eyebrow">Status</span><div>{{ $user->status->value ?? $user->status }}</div></div>
        <div><span class="figma-eyebrow">Roles</span><div>{{ $user->getRoleNames()->implode(', ') ?: '—' }}</div></div>
    </div>
    <div class="mt-6">
        <a href="{{ route('admin.users.edit', $user) }}" class="figma-button">Edit</a>
    </div>
</div>
@endsection
