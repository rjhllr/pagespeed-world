@php
    $pathInfo = request()->getPathInfo();
    $pathWithoutLocale = preg_replace('#^/(en|de)#', '', $pathInfo) ?: '/';
@endphp
<header class="sticky top-0 z-30 backdrop-blur bg-slate-950/70 border-b border-white/5">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between gap-6">
            <a href="{{ url(app()->getLocale()) }}" class="flex items-center gap-2 font-semibold text-white">
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-brand-500 to-cyan-400 text-white shadow-card">ψ</span>
                <span class="text-lg">{{ config('app.name', 'pagespeed.world') }}</span>
            </a>
            <nav class="hidden md:flex items-center gap-6 text-sm font-medium text-slate-200">
                <a href="{{ route('marketing.home', app()->getLocale()) }}#features" class="hover:text-white">{{ __('marketing.nav.features') }}</a>
                <a href="{{ route('marketing.home', app()->getLocale()) }}#pricing" class="hover:text-white">{{ __('marketing.nav.pricing') }}</a>
                <a href="{{ route('marketing.home', app()->getLocale()) }}#faq" class="hover:text-white">{{ __('marketing.nav.faq') }}</a>
                <a href="{{ route('marketing.blog.index', app()->getLocale()) }}" class="hover:text-white">{{ __('marketing.nav.blog') }}</a>
            </nav>
            <div class="flex items-center gap-3">
                <form method="GET" action="{{ url('en' . $pathWithoutLocale) }}">
                    <button type="submit" class="text-xs font-semibold px-3 py-2 rounded-lg border border-white/10 text-slate-200 hover:text-white hover:border-white/30 transition {{ app()->getLocale() === 'en' ? 'bg-white/10' : '' }}">EN</button>
                </form>
                <form method="GET" action="{{ url('de' . $pathWithoutLocale) }}">
                    <button type="submit" class="text-xs font-semibold px-3 py-2 rounded-lg border border-white/10 text-slate-200 hover:text-white hover:border-white/30 transition {{ app()->getLocale() === 'de' ? 'bg-white/10' : '' }}">DE</button>
                </form>
                <a href="{{ route('login') }}" class="hidden sm:inline-flex items-center text-sm font-semibold text-white hover:text-white/80">
                    {{ __('marketing.nav.sign_in') }}
                </a>
                @include('marketing.partials.primary-button', [
                    'href' => route('marketing.home', app()->getLocale()) . '#pricing',
                    'label' => __('marketing.nav.get_started'),
                    'size' => 'sm'
                ])
            </div>
        </div>
    </div>
</header>
