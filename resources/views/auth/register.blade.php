@extends('layouts.guest')

@section('content')
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div>
            <label for="name" class="block font-bold text-sm text-figma-ink mb-1">Name</label>
            <input id="name" class="block mt-1 w-full border border-figma-hairline focus:border-figma-hairline focus:ring-primary rounded-full px-4 py-2 text-gray-600" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" />
            @error('name')
                <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mt-4">
            <label for="email" class="block font-bold text-sm text-figma-ink mb-1">Email</label>
            <input id="email" class="block mt-1 w-full border border-figma-hairline focus:border-figma-hairline focus:ring-primary rounded-full px-4 py-2 text-gray-600" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" />
            @error('email')
                <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mt-4">
            <label for="password" class="block font-bold text-sm text-figma-ink mb-1">Password</label>
            <input id="password" class="block mt-1 w-full border border-figma-hairline focus:border-figma-hairline focus:ring-primary rounded-full px-4 py-2 text-gray-600" type="password" name="password" required autocomplete="new-password" />
            @error('password')
                <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mt-4">
            <label for="password_confirmation" class="block font-bold text-sm text-figma-ink mb-1">Confirm Password</label>
            <input id="password_confirmation" class="block mt-1 w-full border border-figma-hairline focus:border-figma-hairline focus:ring-primary rounded-full px-4 py-2 text-gray-600" type="password" name="password_confirmation" required autocomplete="new-password" />
            @error('password_confirmation')
                <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="flex items-center justify-end mt-6">
            <a class="underline text-sm text-gray-600 hover:text-figma-ink rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary" href="{{ route('login') }}">
                Already registered?
            </a>

            <button type="submit" class="ml-3 inline-flex items-center px-6 py-2 bg-figma-ink border border-transparent rounded-full font-bold text-xs text-figma-ink uppercase tracking-widest hover:bg-figma-ink focus:bg-green-strong active:bg-green-strong focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition ease-in-out duration-150">
                Register
            </button>
        </div>
    </form>
@endsection
