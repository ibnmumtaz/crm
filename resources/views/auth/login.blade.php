@extends('layouts.guest')

@section('content')
    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <label for="email" class="block font-bold text-sm text-figma-ink mb-1">Email</label>
            <input id="email" class="block mt-1 w-full border border-figma-hairline focus:border-figma-hairline focus:ring-primary rounded-full px-4 py-2 text-gray-600" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" />
            @error('email')
                <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mt-4">
            <label for="password" class="block font-bold text-sm text-figma-ink mb-1">Password</label>
            <input id="password" class="block mt-1 w-full border border-figma-hairline focus:border-figma-hairline focus:ring-primary rounded-full px-4 py-2 text-gray-600" type="password" name="password" required autocomplete="current-password" />
            @error('password')
                <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center text-gray-600 text-sm">
                <input id="remember_me" type="checkbox" class="rounded border-figma-hairline text-figma-ink shadow-sm focus:ring-primary" name="remember">
                <span class="ml-2">Remember me</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-6">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-figma-ink rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary" href="{{ route('password.request') }}">
                    Forgot your password?
                </a>
            @endif

            <button type="submit" class="ml-3 inline-flex items-center px-6 py-2 bg-figma-ink border border-transparent rounded-full font-bold text-xs text-figma-ink uppercase tracking-widest hover:bg-figma-ink focus:bg-green-strong active:bg-green-strong focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition ease-in-out duration-150">
                Log in
            </button>
        </div>

        <div class="mt-4 text-center">
            <a href="{{ route('register') }}" class="underline text-sm text-gray-600 hover:text-figma-ink">Don't have an account? Register</a>
        </div>
    </form>
@endsection
