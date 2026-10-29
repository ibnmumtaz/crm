@extends('layouts.app')
@section('content')
    <div class="bg-figma-soft border border-figma-hairline rounded-md p-6 max-w-2xl">
        <h2 class="text-2xl font-bold text-figma-ink mb-6">Edit Target</h2>
        <form method="POST" action="{{ route('targets.update', $target) }}">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label for="type" class="block font-bold text-sm text-figma-ink mb-1">Type *</label>
                <select id="type" name="type" class="block w-full border border-figma-hairline focus:border-figma-hairline focus:ring-primary rounded-md px-4 py-2 text-gray-600" required>
                    @foreach(\App\Enums\TargetType::cases() as $t)
                        <option value="{{ $t->value }}" @selected(old('type', $target->type->value ?? $target->type) === $t->value)>{{ $t->value }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label for="period" class="block font-bold text-sm text-figma-ink mb-1">Period *</label>
                <select id="period" name="period" class="block w-full border border-figma-hairline focus:border-figma-hairline focus:ring-primary rounded-md px-4 py-2 text-gray-600" required>
                    @foreach(\App\Enums\TargetPeriod::cases() as $p)
                        <option value="{{ $p->value }}" @selected(old('period', $target->period->value ?? $target->period) === $p->value)>{{ $p->value }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label for="period_start" class="block font-bold text-sm text-figma-ink mb-1">Start *</label>
                <input id="period_start" name="period_start" type="date" value="{{ old('period_start', $target->period_start->format('Y-m-d')) }}" class="block w-full border border-figma-hairline focus:border-figma-hairline focus:ring-primary rounded-full px-4 py-2 text-gray-600" required />
            </div>
            <div class="mb-4">
                <label for="period_end" class="block font-bold text-sm text-figma-ink mb-1">End *</label>
                <input id="period_end" name="period_end" type="date" value="{{ old('period_end', $target->period_end->format('Y-m-d')) }}" class="block w-full border border-figma-hairline focus:border-figma-hairline focus:ring-primary rounded-full px-4 py-2 text-gray-600" required />
            </div>
            <div class="mb-4">
                <label for="amount" class="block font-bold text-sm text-figma-ink mb-1">Amount *</label>
                <input id="amount" name="amount" type="number" step="0.01" value="{{ old('amount', $target->amount) }}" class="block w-full border border-figma-hairline focus:border-figma-hairline focus:ring-primary rounded-full px-4 py-2 text-gray-600" required />
            </div>
            <div class="mb-4">
                <label for="user_id" class="block font-bold text-sm text-figma-ink mb-1">User</label>
                <select id="user_id" name="user_id" class="block w-full border border-figma-hairline focus:border-figma-hairline focus:ring-primary rounded-md px-4 py-2 text-gray-600">
                    <option value="">-- None --</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" @selected(old('user_id', $target->user_id) == $u->id)>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label for="team_id" class="block font-bold text-sm text-figma-ink mb-1">Team</label>
                <select id="team_id" name="team_id" class="block w-full border border-figma-hairline focus:border-figma-hairline focus:ring-primary rounded-md px-4 py-2 text-gray-600">
                    <option value="">-- None --</option>
                    @foreach($teams as $t)
                        <option value="{{ $t->id }}" @selected(old('team_id', $target->team_id) == $t->id)>{{ $t->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-figma-ink text-figma-canvas px-6 py-2 rounded-full font-bold text-sm hover:bg-figma-ink transition">Update</button>
                <a href="{{ route('targets.index') }}" class="bg-figma-soft border border-figma-hairline text-gray-600 px-6 py-2 rounded-full font-bold text-sm hover:bg-figma-soft transition">Cancel</a>
            </div>
        </form>
    </div>
@endsection
