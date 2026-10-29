@extends('layouts.app')
@section('content')
    <div class="bg-figma-soft border border-figma-hairline rounded-md p-6 max-w-2xl">
        <h2 class="text-2xl font-bold text-figma-ink mb-6">Edit Team</h2>
        <form method="POST" action="{{ route('admin.teams.update', $team) }}">
            @csrf @method('PUT')
            <div class="mb-4"><label class="block text-figma-ink mb-1">Name *</label><input name="name" value="{{ old('name', $team->name) }}" class="block w-full border border-figma-hairline rounded-full px-4 py-2 text-gray-600" required /></div>
            <div class="mb-4"><label class="block text-figma-ink mb-1">Members</label>
                <select name="users[]" multiple class="block w-full border border-figma-hairline rounded-md px-4 py-2 text-gray-600">
                    @foreach($users as $u)<option value="{{ $u->id }}" @selected($team->users->contains($u))>{{ $u->name }}</option>@endforeach
                </select>
            </div>
            <button type="submit" class="bg-figma-ink text-figma-canvas px-6 py-2 rounded-full font-bold text-sm">Update</button>
        </form>
    </div>
@endsection
