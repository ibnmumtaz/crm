<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'CRM') }} - Sales CRM for Growing Teams</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: {
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
                    'figma-magenta': '#ff3d8b',
                },
                fontFamily: {
                    sans: ['Inter', 'system-ui', 'sans-serif'],
                    mono: ['JetBrains Mono', 'monospace'],
                },
                borderRadius: { pill: '50px', full: '9999px' },
            }}
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;700&family=JetBrains+Mono:wght@400&display=swap" rel="stylesheet">

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
<body class="overflow-x-hidden bg-figma-canvas text-figma-ink font-sans antialiased">
    <nav class="bg-figma-canvas border-b border-figma-hairline sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
            <a href="{{ route('marketing.home') }}" class="text-xl font-bold text-figma-ink">CRM</a>
            <div class="hidden md:flex items-center gap-6 text-sm">
                <a href="{{ route('marketing.features') }}" class="text-figma-ink hover:opacity-70">Features</a>
                <a href="{{ route('marketing.solutions') }}" class="text-figma-ink hover:opacity-70">Solutions</a>
                <a href="{{ route('marketing.pricing') }}" class="text-figma-ink hover:opacity-70">Pricing</a>
                <a href="{{ route('marketing.contact') }}" class="text-figma-ink hover:opacity-70">Contact</a>
                <a href="{{ route('login') }}" class="text-figma-ink hover:opacity-70">Sign in</a>
                <a href="{{ route('register') }}" class="bg-figma-ink text-figma-canvas px-5 py-2 rounded-pill font-medium">Get started</a>
            </div>
        </div>
    </nav>
    <main>
        @yield('content')
    </main>
    <footer class="bg-figma-canvas border-t border-figma-hairline mt-24 py-16">
        <div class="max-w-7xl mx-auto px-6 text-sm text-figma-ink">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <div class="font-bold mb-2">CRM</div>
                    <p class="text-sm">Sales management for growing teams.</p>
                </div>
                <div>
                    <div class="font-mono text-xs uppercase tracking-widest mb-2">Product</div>
                    <ul class="space-y-1"><li><a href="{{ route('marketing.features') }}">Features</a></li><li><a href="{{ route('marketing.pricing') }}">Pricing</a></li></ul>
                </div>
                <div>
                    <div class="font-mono text-xs uppercase tracking-widest mb-2">Company</div>
                    <ul class="space-y-1"><li><a href="{{ route('marketing.about') }}">About</a></li><li><a href="{{ route('marketing.contact') }}">Contact</a></li></ul>
                </div>
                <div>
                    <div class="font-mono text-xs uppercase tracking-widest mb-2">Legal</div>
                    <ul class="space-y-1"><li><a href="{{ route('marketing.privacy') }}">Privacy</a></li><li><a href="{{ route('marketing.terms') }}">Terms</a></li></ul>
                </div>
            </div>
            <div class="mt-12 text-xs">© {{ date('Y') }} CRM. All rights reserved.</div>
        </div>
    </footer>
</body>
</html>
