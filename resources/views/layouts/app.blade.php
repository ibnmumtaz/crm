<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'CRM') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'figma-ink': '#000000',
                        'figma-canvas': '#ffffff',
                        'figma-hairline': '#e6e6e6',
                        'figma-soft': '#f7f7f5',
                        'figma-lime': '#dceeb1',
                        'figma-lilac': '#c5b0f4',
                        'figma-cream': '#f4ecd6',
                        'figma-pink': '#efd4d4',
                        'figma-mint': '#c8e6cd',
                        'figma-coral': '#f3c9b6',
                        'figma-navy': '#1f1d3d',
                    },
                    fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'], mono: ['JetBrains Mono', 'monospace'] },
                    borderRadius: { pill: '50px' },
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;700&family=JetBrains+Mono:wght@400&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root{--ink:#000;--canvas:#fff;--line:#e6e6e6;--soft:#f7f7f5;--lime:#dceeb1;--lilac:#c5b0f4;--cream:#f4ecd6;--pink:#efd4d4;--mint:#c8e6cd;--coral:#f3c9b6;--navy:#1f1d3d;--magenta:#ff3d8b}
        *{box-sizing:border-box}
        html{scroll-behavior:smooth}
        body{font-family:Inter,ui-sans-serif,system-ui,sans-serif;letter-spacing:-.015em}
        a,button,input,select,textarea{transition:background-color .18s ease,color .18s ease,border-color .18s ease,box-shadow .18s ease,transform .18s ease}
        a:focus-visible,button:focus-visible,input:focus-visible,select:focus-visible,textarea:focus-visible{outline:3px solid #c5b0f4;outline-offset:3px}
        h1,h2,h3{letter-spacing:-.045em}
        input:not([type=checkbox]):not([type=radio]),select,textarea{border:1px solid #dededb!important;border-radius:10px!important;padding:.75rem .9rem!important;background:#fff!important;color:#111!important;max-width:100%}
        input:focus,select:focus,textarea:focus{border-color:#000!important;box-shadow:0 0 0 3px rgba(197,176,244,.45)!important}
        button,[role=button]{cursor:pointer}
        main a:not([class]){text-decoration:underline;text-underline-offset:3px}
        .figma-card{border:1px solid var(--line);border-radius:18px;background:#fff;padding:24px}
        .figma-eyebrow{font-family:'JetBrains Mono',monospace;font-size:11px;text-transform:uppercase;letter-spacing:.12em}
        .figma-button{display:inline-flex;align-items:center;justify-content:center;gap:.5rem;padding:11px 20px;border-radius:999px;background:#000;color:#fff;font-weight:600}
        .figma-button:hover{background:#282828;transform:translateY(-1px)}
        .figma-button-outline{display:inline-flex;align-items:center;justify-content:center;padding:10px 19px;border:1px solid #d8d8d8;border-radius:999px;background:#fff;color:#000;font-weight:600}
        .figma-button-outline:hover{background:#f7f7f5;border-color:#000}
        .figma-panel{border-radius:20px;padding:clamp(24px,4vw,48px)}
        ::selection{background:#dceeb1;color:#000}
        @media(max-width:640px){.figma-card{padding:18px}.figma-panel{border-radius:16px}}
        @media(prefers-reduced-motion:reduce){*,*::before,*::after{scroll-behavior:auto!important;transition:none!important;animation:none!important}}
    </style>

</head>
<body class="overflow-x-hidden bg-figma-canvas font-sans antialiased text-figma-ink">
    <div class="min-h-screen flex">
        <button id="sidebarToggle" class="md:hidden fixed top-4 left-4 z-50 bg-figma-ink text-figma-canvas p-2 rounded-full">☰</button>
        <aside id="sidebar" class="w-64 bg-figma-canvas border-r border-figma-hairline flex flex-col fixed top-0 left-0 h-screen z-40 transform -translate-x-full md:translate-x-0 transition-transform">
            <div class="p-4 border-b border-figma-hairline">
                <a href="{{ route('dashboard') }}" class="text-xl font-bold text-figma-ink">CRM</a>
            </div>
            <nav class="flex-1 p-3 space-y-1">
                @php
                    $links = [
                        ['route' => 'dashboard', 'label' => 'Dashboard'],
                        ['route' => 'leads.index', 'label' => 'Leads'],
                        ['route' => 'tasks.index', 'label' => 'Tasks'],
                        ['route' => 'communications.index', 'label' => 'Communications'],
                        ['route' => 'customers.index', 'label' => 'Customers'],
                        ['route' => 'targets.index', 'label' => 'Targets'],
                        ['route' => 'pipelines.index', 'label' => 'Pipelines'],
                        ['route' => 'reports.index', 'label' => 'Reports'],
                    ];
                @endphp
                @foreach($links as $link)
                    @php
                        $isActive = request()->routeIs($link['route']) || request()->routeIs(str_replace('.index', '.*', $link['route']));
                    @endphp
                    <a href="{{ route($link['route']) }}"
                       class="flex items-center gap-3 px-4 py-2.5 rounded-pill text-sm font-medium transition-colors {{ $isActive ? 'bg-figma-ink text-figma-canvas' : 'text-figma-ink hover:bg-figma-soft' }}">
                        <span>{{ $link['label'] }}</span>
                    </a>
                @endforeach
                @if(auth()->user()->can('user.manage'))
                    <div class="pt-4 mt-4 border-t border-figma-hairline">
                        <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-pill text-sm font-medium {{ request()->routeIs('admin.*') ? 'bg-figma-ink text-figma-canvas' : 'text-figma-ink hover:bg-figma-soft' }}">Admin</a>
                        <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-pill text-sm font-medium {{ request()->routeIs('settings.*') ? 'bg-figma-ink text-figma-canvas' : 'text-figma-ink hover:bg-figma-soft' }}">Settings</a>
                        <a href="{{ route('admin.bookings.admin') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-pill text-sm font-medium {{ request()->routeIs('bookings.admin') ? 'bg-figma-ink text-figma-canvas' : 'text-figma-ink hover:bg-figma-soft' }}">Bookings</a>
                    </div>
                @endif
            </nav>
            <div class="p-4 border-t border-figma-hairline">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2.5 rounded-pill text-sm font-medium text-figma-ink hover:bg-figma-soft transition-colors">
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        <main class="flex-1 overflow-y-auto md:ml-64 bg-figma-canvas">
            <div class="py-6">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    @if(session('success'))
                        <div class="mb-4 bg-figma-soft border border-figma-hairline text-figma-ink px-4 py-3 rounded-lg">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="mb-4 bg-figma-pink border border-figma-hairline text-figma-ink px-4 py-3 rounded-lg">
                            {{ session('error') }}
                        </div>
                    @endif
                    @yield('content')
                </div>
            </div>
        </main>
    </div>
    <script>
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('-translate-x-full');
        });
    </script>
</body>
</html>
