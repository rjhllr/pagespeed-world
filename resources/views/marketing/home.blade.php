@extends('layouts.marketing')

@section('content')
<section class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 pt-16 pb-20 lg:pt-20 lg:pb-24" id="top">
    <div class="grid gap-12 lg:grid-cols-2 lg:items-center">
        <div class="space-y-6">
            <p class="inline-flex items-center rounded-full bg-white/5 px-3 py-1 text-xs font-semibold text-cyan-200 ring-1 ring-white/10">
                {{ __('marketing.home.hero.eyebrow') }}
            </p>
            <h1 class="text-4xl font-semibold text-white sm:text-5xl leading-tight">
                {{ __('marketing.home.hero.title') }}
            </h1>
            <p class="text-lg text-slate-300 max-w-2xl">
                {{ __('marketing.home.hero.subtitle') }}
            </p>
            <div class="flex flex-wrap gap-3">
                @include('marketing.partials.primary-button', [
                    'href' => '#pricing',
                    'label' => __('marketing.home.hero.primary'),
                    'size' => 'lg'
                ])
                <a href="#features" class="inline-flex items-center gap-2 text-sm font-semibold text-cyan-200 hover:text-white">
                    {{ __('marketing.home.hero.secondary') }}
                    <span aria-hidden="true">→</span>
                </a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-6">
                <div class="rounded-2xl border border-white/5 bg-white/5 px-4 py-4">
                    <p class="text-2xl font-semibold text-white">99.9%</p>
                    <p class="text-sm text-slate-400">{{ __('marketing.home.metrics.uptime') }}</p>
                </div>
                <div class="rounded-2xl border border-white/5 bg-white/5 px-4 py-4">
                    <p class="text-2xl font-semibold text-white">300+</p>
                    <p class="text-sm text-slate-400">{{ __('marketing.home.metrics.sites') }}</p>
                </div>
                <div class="rounded-2xl border border-white/5 bg-white/5 px-4 py-4">
                    <p class="text-2xl font-semibold text-white">18%</p>
                    <p class="text-sm text-slate-400">{{ __('marketing.home.metrics.savings') }}</p>
                </div>
            </div>
        </div>
        <div class="relative">
            <div class="absolute -left-8 -top-6 h-48 w-48 rounded-full bg-brand-500/30 blur-3xl"></div>
            <div class="rounded-3xl border border-white/5 bg-slate-900/60 p-6 shadow-card backdrop-blur">
                <div class="flex items-center justify-between text-xs text-slate-400 mb-4">
                    <span>pagespeed.world</span>
                    <span>Core Web Vitals</span>
                </div>
                <div class="space-y-4">
                    <div class="flex items-center justify-between rounded-2xl border border-white/5 bg-white/5 px-4 py-3">
                        <span class="text-sm text-slate-200">LCP</span>
                        <span class="text-xl font-semibold text-emerald-300">1.8s</span>
                    </div>
                    <div class="flex items-center justify-between rounded-2xl border border-white/5 bg-white/5 px-4 py-3">
                        <span class="text-sm text-slate-200">CLS</span>
                        <span class="text-xl font-semibold text-emerald-300">0.04</span>
                    </div>
                    <div class="flex items-center justify-between rounded-2xl border border-white/5 bg-white/5 px-4 py-3">
                        <span class="text-sm text-slate-200">TTFB</span>
                        <span class="text-xl font-semibold text-amber-300">220ms</span>
                    </div>
                    <div class="flex items-center justify-between rounded-2xl border border-white/5 bg-white/5 px-4 py-3">
                        <span class="text-sm text-slate-200">Bundle size</span>
                        <span class="text-xl font-semibold text-emerald-300">176kb</span>
                    </div>
                </div>
                <div class="mt-6 flex items-center justify-between rounded-xl border border-white/5 bg-gradient-to-r from-brand-600/60 to-cyan-500/50 px-4 py-3">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-100">Weekly report</p>
                        <p class="text-sm text-slate-100">No regressions detected</p>
                    </div>
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-white/10 text-white">✓</span>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="features" class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 pb-16 space-y-8">
    <div class="flex items-center justify-between gap-4">
        <div>
            <p class="text-sm font-semibold text-cyan-200">{{ __('marketing.nav.features') }}</p>
            <h2 class="mt-2 text-3xl font-semibold text-white">What makes us different</h2>
        </div>
        <a href="#pricing" class="hidden sm:inline-flex items-center gap-2 text-sm font-semibold text-cyan-200 hover:text-white">
            {{ __('marketing.nav.pricing') }} →
        </a>
    </div>
    <div class="grid gap-6 md:grid-cols-3">
        @foreach(__('marketing.home.features') as $feature)
            <div class="rounded-2xl border border-white/5 bg-white/5 p-6 shadow-card">
                <h3 class="text-lg font-semibold text-white mb-2">{{ $feature['title'] }}</h3>
                <p class="text-sm text-slate-300">{{ $feature['body'] }}</p>
            </div>
        @endforeach
    </div>
</section>

<section id="pricing" class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 pb-16">
    <div class="rounded-3xl border border-white/5 bg-gradient-to-br from-slate-900/70 to-slate-900/40 p-8 shadow-card">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold text-cyan-200">{{ __('marketing.nav.pricing') }}</p>
                <h3 class="text-2xl font-semibold text-white">{{ __('marketing.pricing.description') }}</h3>
                <p class="text-sm text-slate-300 mt-1">{{ __('marketing.pricing.note') }}</p>
            </div>
            @include('marketing.partials.primary-button', [
                'href' => '#pricing',
                'label' => __('marketing.home.cta.primary'),
                'size' => 'md'
            ])
        </div>
    </div>
</section>

<section id="faq" class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 pb-16">
    <div class="rounded-3xl border border-white/5 bg-white/5 p-8 shadow-card space-y-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-sm font-semibold text-cyan-200">{{ __('marketing.nav.faq') }}</p>
                <h3 class="text-2xl font-semibold text-white">{{ __('marketing.faq.title') }}</h3>
                <p class="text-sm text-slate-300">{{ __('marketing.faq.description') }}</p>
            </div>
            <a href="#contact" class="hidden sm:inline-flex items-center gap-2 text-sm font-semibold text-cyan-200 hover:text-white">
                {{ __('marketing.footer.contact') }} →
            </a>
        </div>
        <div class="grid gap-4">
            @foreach(__('marketing.faq.items') as $item)
                <div class="rounded-2xl border border-white/5 bg-slate-900/60 p-4">
                    <p class="text-sm font-semibold text-white">{{ $item['q'] }}</p>
                    <p class="text-sm text-slate-300 mt-2">{{ $item['a'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section id="contact" class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 pb-20">
    <div class="rounded-3xl border border-white/5 bg-gradient-to-r from-brand-600/70 to-cyan-500/60 p-8 shadow-card">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="space-y-1">
                <h3 class="text-2xl font-semibold text-white">{{ __('marketing.home.cta.title') }}</h3>
                <p class="text-sm text-slate-100">{{ __('marketing.home.cta.subtitle') }}</p>
            </div>
            <div class="flex gap-3">
                @include('marketing.partials.primary-button', [
                    'href' => '#pricing',
                    'label' => __('marketing.home.cta.primary'),
                    'size' => 'md'
                ])
                <a href="{{ url(app()->getLocale() . '/blog') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-white/90 hover:text-white">
                    {{ __('marketing.home.cta.secondary') }} →
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
