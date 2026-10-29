@extends('layouts.app')
@section('content')
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-figma-ink">Customers</h2>
        <a href="{{ route('customers.create') }}" class="bg-figma-ink text-figma-canvas px-4 py-2 rounded-full font-bold text-sm">Add Customer</a>
    </div>
    <form method="GET" class="mb-4">
        <input name="search" value="{{ request('search') }}" placeholder="Search customers..." class="border border-figma-hairline rounded-full px-4 py-2 text-gray-600 bg-figma-soft" />
    </form>
    <div class="bg-figma-soft border border-figma-hairline rounded-md overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-figma-soft text-gray-600">
                <tr>
                    <th class="px-4 py-3 text-left">Name</th>
                    <th class="px-4 py-3 text-left">Company</th>
                    <th class="px-4 py-3 text-left">Email</th>
                    <th class="px-4 py-3 text-left">Phone</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $c)
                    <tr class="border-t border-figma-hairline">
                        <td class="px-4 py-3 text-figma-ink"><a href="{{ route('customers.show', $c) }}" class="text-figma-ink">{{ $c->name }}</a></td>
                        <td class="px-4 py-3 text-gray-600">{{ $c->company }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $c->email }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $c->phone }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('customers.edit', $c) }}" class="text-gray-600 hover:text-figma-ink mr-3">Edit</a>
                            <form method="POST" action="{{ route('customers.destroy', $c) }}" class="inline" onsubmit="return confirm('Delete customer?')">@csrf @method('DELETE')<button class="text-red-600">Delete</button></form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-6 text-center text-gray-600">No customers found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $customers->links() }}</div>
@endsection
