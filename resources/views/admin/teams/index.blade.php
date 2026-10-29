@extends('layouts.app')
@section('content')
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-figma-ink">Teams</h2>
        <a href="{{ route('admin.teams.create') }}" class="bg-figma-ink text-figma-canvas px-4 py-2 rounded-full font-bold text-sm">Add Team</a>
    </div>
    <div class="bg-figma-soft border border-figma-hairline rounded-md overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-figma-soft text-gray-600"><tr><th class="px-4 py-3 text-left">Name</th><th class="px-4 py-3 text-left">Members</th><th class="px-4 py-3"></th></tr></thead>
            <tbody>
                @foreach($teams as $t)
                    <tr class="border-t border-figma-hairline">
                        <td class="px-4 py-3 text-figma-ink">{{ $t->name }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $t->users_count }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.teams.edit', $t) }}" class="text-gray-600 hover:text-figma-ink mr-3">Edit</a>
                            <form method="POST" action="{{ route('admin.teams.destroy', $t) }}" class="inline" onsubmit="return confirm('Delete team?')">@csrf @method('DELETE')<button class="text-red-600">Delete</button></form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
