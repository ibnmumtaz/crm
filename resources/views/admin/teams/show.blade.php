@extends('layouts.app')
@section('content')
<div class="figma-card max-w-2xl">
    <h1 class="text-2xl font-bold mb-4">{{ $team->name }}</h1>
    <div class="space-y-2 text-sm">
        <div><span class="figma-eyebrow">Manager</span><div>{{ optional($team->manager)->name ?? '—' }}</div></div>
        <div><span class="figma-eyebrow">Members</span>
            <ul class="mt-2 space-y-1">
                @foreach($team->users as $member)
                    <li>{{ $member->name }} ({{ $member->email }})</li>
                @endforeach
            </ul>
        </div>
    </div>
    <div class="mt-6">
        <a href="{{ route('admin.teams.edit', $team) }}" class="figma-button">Edit</a>
    </div>
</div>
@endsection
