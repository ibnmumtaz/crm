@extends('layouts.app')

@section('content')
    <div class="bg-figma-soft border border-figma-hairline rounded-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-figma-ink">Leads</h2>
            <div class="flex gap-2">
                <a href="{{ route('leads.kanban') }}" class="bg-figma-soft border border-figma-hairline text-figma-ink px-4 py-2 rounded-full font-bold text-sm hover:bg-figma-soft transition">Kanban</a>
                <a href="{{ route('leads.create') }}" class="bg-figma-ink text-figma-canvas px-4 py-2 rounded-full font-bold text-sm hover:bg-figma-ink transition">Add Lead</a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-hairline">
                <thead class="bg-figma-soft">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Company</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Value</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-figma-soft divide-y divide-hairline">
                    @forelse($leads ?? [] as $lead)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-figma-ink font-bold">{{ $lead->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ $lead->company }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ $lead->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded-sm bg-figma-soft text-figma-ink font-bold">
                                    {{ $lead->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-figma-ink font-bold">${{ number_format($lead->value, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold">
                                <a href="{{ route('leads.show', $lead) }}" class="text-figma-ink hover:text-figma-ink mr-3">View</a>
                                <a href="{{ route('leads.edit', $lead) }}" class="text-gray-600 hover:text-figma-ink">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-600">No leads found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(isset($leads) && method_exists($leads, 'links'))
            <div class="mt-4">
                {{ $leads->links() }}
            </div>
        @endif
    </div>
@endsection
