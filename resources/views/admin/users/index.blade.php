@extends('layouts.app')
@section('content')
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-figma-ink">Users</h2>
        <a href="{{ route('admin.users.create') }}" class="bg-figma-ink text-figma-canvas px-4 py-2 rounded-full font-bold text-sm">Add User</a>
    </div>
    <div class="bg-figma-soft border border-figma-hairline rounded-md overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-figma-soft text-gray-600"><tr><th class="px-4 py-3 text-left">Name</th><th class="px-4 py-3 text-left">Email</th><th class="px-4 py-3 text-left">Roles</th><th class="px-4 py-3"></th></tr></thead>
            <tbody>
                @foreach($users as $u)
                    <tr class="border-t border-figma-hairline">
                        <td class="px-4 py-3 text-figma-ink">{{ $u->name }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $u->email }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $u->getRoleNames()->implode(', ') }}</td>
                        <td class="px-4 py-3 text-right"><a href="{{ route('admin.users.edit', $u) }}" class="text-gray-600 hover:text-figma-ink">Edit</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $users->links() }}</div>
@endsection
