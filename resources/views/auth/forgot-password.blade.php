@extends('layouts.guest')

@section('content')
    <div class="mb-4 text-sm text-gray-600">
        Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.
    </div>

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div>
            <label for="email" class="block font-bold text-sm text-figma-ink mb-1">Email</label>
            <input id="email" class="block mt-1 w-full border border-figma-hairline focus:border-figma-hairline focus:ring-primary rounded-full px-4 py-2 text-gray-600" type="email" name="email" value="{{ old('email') }}" required autofocus />
            @error('email')
                <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="flex items-center justify-end mt-6">
            <button type="submit" class="inline-flex items-center px-6 py-2 bg-figma-ink border border-transparent rounded-full font-bold text-xs text-figma-ink uppercase tracking-widest hover:bg-figma-ink focus:bg-green-strong active:bg-green-strong focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition ease-in-out duration-150">
                Email Password Reset Link
            </button>
        </div>
    </form>
@endsection
