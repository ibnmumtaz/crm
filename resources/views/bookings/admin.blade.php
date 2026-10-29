@extends('layouts.app')
@section('content')
    <h2 class="text-2xl font-bold text-figma-ink mb-6">Bookings</h2>
    <div class="bg-figma-soft border border-figma-hairline rounded-md overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-figma-soft text-gray-600"><tr><th class="px-4 py-3 text-left">Type</th><th class="px-4 py-3 text-left">Name</th><th class="px-4 py-3 text-left">Email</th><th class="px-4 py-3 text-left">Starts</th><th class="px-4 py-3 text-left">Status</th></tr></thead>
            <tbody>
                @foreach($bookings as $b)
                    <tr class="border-t border-figma-hairline">
                        <td class="px-4 py-3 text-figma-ink">{{ $b->meetingType->name }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $b->name }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $b->email }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $b->starts_at }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $b->status }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $bookings->links() }}</div>
@endsection
