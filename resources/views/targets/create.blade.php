@extends('layouts.app')

@section('content')
    <div class="bg-figma-soft border border-figma-hairline rounded-md p-6 max-w-2xl">
        <h2 class="text-2xl font-bold text-figma-ink mb-6">Create Target</h2>

        <form method="POST" action="{{ route('targets.store') }}">
            @csrf

            <div class="mb-4">
                <label for="type" class="block font-bold text-sm text-figma-ink mb-1">Type *</label>
                <select id="type" name="type" class="block w-full border border-figma-hairline focus:border-figma-hairline focus:ring-primary rounded-md px-4 py-2 text-gray-600" required>
                    @foreach(\App\Enums\TargetType::cases() as $t)
                        <option value="{{ $t->value }}" @selected(old('type') === $t->value)>{{ ucfirst(str_replace('_', ' ', $t->value)) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="period" class="block font-bold text-sm text-figma-ink mb-1">Period *</label>
                <select id="period" name="period" class="block w-full border border-figma-hairline focus:border-figma-hairline focus:ring-primary rounded-md px-4 py-2 text-gray-600" required>
                    @foreach(\App\Enums\TargetPeriod::cases() as $p)
                        <option value="{{ $p->value }}" @selected(old('period') === $p->value)>{{ ucfirst($p->value) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="period_start" class="block font-bold text-sm text-figma-ink mb-1">Period Start *</label>
                <input id="period_start" name="period_start" type="date" value="{{ old('period_start') }}" class="block w-full border border-figma-hairline focus:border-figma-hairline focus:ring-primary rounded-full px-4 py-2 text-gray-600" required />
            </div>

            <div class="mb-4">
                <label for="period_end" class="block font-bold text-sm text-figma-ink mb-1">Period End *</label>
                <input id="period_end" name="period_end" type="date" value="{{ old('period_end') }}" class="block w-full border border-figma-hairline focus:border-figma-hairline focus:ring-primary rounded-full px-4 py-2 text-gray-600" required />
            </div>

            <div class="mb-4">
                <label for="amount" class="block font-bold text-sm text-figma-ink mb-1">Amount *</label>
                <input id="amount" name="amount" type="number" step="0.01" value="{{ old('amount') }}" class="block w-full border border-figma-hairline focus:border-figma-hairline focus:ring-primary rounded-full px-4 py-2 text-gray-600" required />
            </div>

            <div class="flex gap-2">
                <button type="submit" class="bg-figma-ink text-figma-canvas px-6 py-2 rounded-full font-bold text-sm hover:bg-figma-ink transition">Create Target</button>
                <a href="{{ route('targets.index') }}" class="bg-figma-soft border border-figma-hairline text-gray-600 px-6 py-2 rounded-full font-bold text-sm hover:bg-figma-soft transition">Cancel</a>
            </div>
        </form>
    </div>
@endsection
