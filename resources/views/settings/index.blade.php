@extends('layouts.app')
@section('content')
    <h2 class="text-2xl font-bold text-figma-ink mb-6">Settings</h2>
    <div class="bg-figma-soft border border-figma-hairline rounded-md p-6 max-w-2xl">
        <ul class="text-gray-600 space-y-2">
            <li>Environment: {{ $config['app_env'] }}</li>
            <li>Debug: {{ $config['app_debug'] ? 'enabled' : 'disabled' }}</li>
            <li>Mail mailer: {{ $config['mail_mailer'] }}</li>
        </ul>
        <p class="text-gray-600 text-sm mt-4">Profile and password settings are available via your account menu.</p>
    </div>
@endsection
