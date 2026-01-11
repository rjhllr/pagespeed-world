@props([
    'title' => 'pagespeed.world',
    'subnav' => [],
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f1f6ff',
                            100: '#e4ecff',
                            200: '#c6d5ff',
                            300: '#a7bfff',
                            400: '#7f9dff',
                            500: '#5479f7',
                            600: '#3c5edb',
                            700: '#304bb3',
                            800: '#2a418f',
                            900: '#253872',
                        },
                    },
                    boxShadow: {
                        'card': '0 10px 40px -12px rgba(15, 23, 42, 0.18)',
                    },
                },
            },
        };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>

    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>

    {{ $styles ?? '' }}
    @stack('styles')
    @stack('head')
</head>
<body class="bg-slate-950 text-slate-100 antialiased">
    <div class="relative isolate min-h-screen overflow-hidden">
        <div class="pointer-events-none absolute inset-0 -z-10 bg-[radial-gradient(circle_at_20%_20%,rgba(80,105,255,0.12),transparent_35%),radial-gradient(circle_at_80%_0%,rgba(0,200,255,0.12),transparent_40%),radial-gradient(circle_at_50%_80%,rgba(255,255,255,0.06),transparent_45%)]"></div>

        @auth
            @include('partials.app-nav', ['subnav' => $subnav])
        @endauth

        <main class="min-h-screen">
            {{ $slot }}
        </main>
    </div>

    {{ $scripts ?? '' }}
    @stack('scripts')
</body>
</html>
