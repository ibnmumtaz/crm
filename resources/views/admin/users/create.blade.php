@extends('layouts.app')
@section('content')
    <div class="bg-figma-soft border border-figma-hairline rounded-md p-6 max-w-2xl">
        <h2 class="text-2xl font-bold text-figma-ink mb-6">Create User</h2>
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            <div class="mb-4"><label class="block text-figma-ink mb-1">Name *</label><input name="name" value="{{ old('name') }}" class="block w-full border border-figma-hairline rounded-full px-4 py-2 text-gray-600" required /></div>
            <div class="mb-4"><label class="block text-figma-ink mb-1">Email *</label><input name="email" type="email" value="{{ old('email') }}" class="block w-full border border-figma-hairline rounded-full px-4 py-2 text-gray-600" required /></div>
            <div class="mb-4"><label class="block text-figma-ink mb-1">Password *</label><input name="password" type="password" class="block w-full border border-figma-hairline rounded-full px-4 py-2 text-gray-600" required /></div>
            <div class="mb-4"><label class="block text-figma-ink mb-1">Role *</label>
                <select name="role" class="block w-full border border-figma-hairline rounded-md px-4 py-2 text-gray-600" required>
                    @foreach($roles as $r)<option value="{{ $r->name }}">{{ $r->name }}</option>@endforeach
                </select>
            </div>
            <div class="mb-4"><label class="block text-figma-ink mb-1">Status</label>
                <select name="status" class="block w-full border border-figma-hairline rounded-md px-4 py-2 text-gray-600">
                    <option value="active">active</option><option value="suspended">suspended</option>
                </select>
            </div>
            <button type="submit" class="bg-figma-ink text-figma-canvas px-6 py-2 rounded-full font-bold text-sm">Create</button>
        </form>
    </div>
@endsection
