<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name', 'pagespeed.world') }}</title>
    <meta name="description" content="{{ $meta['description'] ?? __('marketing.meta.description') }}">
    <meta property="og:title" content="{{ $meta['og_title'] ?? ($title ?? config('app.name', 'pagespeed.world')) }}">
    <meta property="og:description" content="{{ $meta['og_description'] ?? ($meta['description'] ?? __('marketing.meta.description')) }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $meta['url'] ?? url()->current() }}">
    <meta property="og:image" content="{{ $meta['image'] ?? asset('favicon.ico') }}">
    @php
        $pathInfo = request()->getPathInfo();
        $pathWithoutLocale = preg_replace('#^/(en|de)#', '', $pathInfo) ?: '/';
    @endphp
    <link rel="alternate" hreflang="en" href="{{ url('en' . $pathWithoutLocale) }}">
    <link rel="alternate" hreflang="de" href="{{ url('de' . $pathWithoutLocale) }}">
    <link rel="alternate" hreflang="x-default" href="{{ url('en' . $pathWithoutLocale) }}">
    <link rel="canonical" href="{{ $meta['url'] ?? url($pathInfo) }}">
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
    <style>
        body { font-family: 'Inter', sans-serif; }
        .article-content p { margin-bottom: 1rem; }
        .article-content h3 { margin-top: 1.25rem; margin-bottom: 0.5rem; font-size: 1.1rem; font-weight: 600; }
        .article-content code { background: rgba(255,255,255,0.07); padding: 0.15rem 0.35rem; border-radius: 0.375rem; }
        .article-content ul { margin-left: 1.25rem; list-style: disc; margin-bottom: 1rem; }
        .article-content li { margin-bottom: 0.35rem; }
    </style>
    @stack('head')
</head>
<body class="bg-slate-950 text-slate-100 antialiased">
    <div class="relative isolate overflow-hidden">
        <div class="pointer-events-none absolute inset-0 -z-10 bg-[radial-gradient(circle_at_20%_20%,rgba(80,105,255,0.12),transparent_35%),radial-gradient(circle_at_80%_0%,rgba(0,200,255,0.12),transparent_40%),radial-gradient(circle_at_50%_80%,rgba(255,255,255,0.06),transparent_45%)]"></div>
        @include('marketing.partials.nav')
        <main class="min-h-screen">
            @hasSection('content')
                @yield('content')
            @else
                {{ $slot ?? '' }}
            @endif
        </main>
        @include('marketing.partials.footer')
    </div>
    @stack('scripts')
</body>
</html>
