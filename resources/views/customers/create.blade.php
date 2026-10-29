@extends('layouts.app')
@section('content')
    <div class="bg-figma-soft border border-figma-hairline rounded-md p-6 max-w-2xl">
        <h2 class="text-2xl font-bold text-figma-ink mb-6">Create Customer</h2>
        <form method="POST" action="{{ route('customers.store') }}">
            @csrf
            <div class="mb-4"><label class="block font-bold text-sm text-figma-ink mb-1">Name *</label><input name="name" value="{{ old('name') }}" class="block w-full border border-figma-hairline rounded-full px-4 py-2 text-gray-600" required /></div>
            <div class="mb-4"><label class="block font-bold text-sm text-figma-ink mb-1">Company</label><input name="company" value="{{ old('company') }}" class="block w-full border border-figma-hairline rounded-full px-4 py-2 text-gray-600" /></div>
            <div class="mb-4"><label class="block font-bold text-sm text-figma-ink mb-1">Email</label><input name="email" type="email" value="{{ old('email') }}" class="block w-full border border-figma-hairline rounded-full px-4 py-2 text-gray-600" /></div>
            <div class="mb-4"><label class="block font-bold text-sm text-figma-ink mb-1">Phone</label><input name="phone" value="{{ old('phone') }}" class="block w-full border border-figma-hairline rounded-full px-4 py-2 text-gray-600" /></div>
            <div class="mb-4"><label class="block font-bold text-sm text-figma-ink mb-1">Lead ID</label><input name="lead_id" value="{{ old('lead_id') }}" class="block w-full border border-figma-hairline rounded-full px-4 py-2 text-gray-600" /></div>
            <div class="flex gap-2">
                <button type="submit" class="bg-figma-ink text-figma-canvas px-6 py-2 rounded-full font-bold text-sm">Create</button>
                <a href="{{ route('customers.index') }}" class="bg-figma-soft border border-figma-hairline text-gray-600 px-6 py-2 rounded-full font-bold text-sm">Cancel</a>
            </div>
        </form>
    </div>
@endsection
