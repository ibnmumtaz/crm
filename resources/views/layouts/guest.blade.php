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
            theme: { extend: {
                colors: {
                    'figma-ink': '#000000',
                    'figma-canvas': '#ffffff',
                    'figma-hairline': '#e6e6e6',
                    'figma-soft': '#f7f7f5',
                    'figma-lime': '#dceeb1',
                    'figma-lilac': '#c5b0f4',
                },
                fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'], mono: ['JetBrains Mono', 'monospace'] },
                borderRadius: { pill: '50px' },
            }}
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;700&family=JetBrains+Mono:wght@400&display=swap" rel="stylesheet">
</head>
<body class="overflow-x-hidden bg-figma-canvas font-sans antialiased text-figma-ink">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        <div>
            <h1 class="text-3xl font-bold text-figma-ink mb-6">CRM System</h1>
        </div>
        <div class="w-full sm:max-w-md mt-6 px-6 py-6 bg-figma-soft border border-figma-hairline overflow-hidden rounded-md">
            @yield('content')
        </div>
    </div>
</body>
</html>
