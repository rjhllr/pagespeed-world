@extends('layouts.marketing')

@section('content')
<section class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 pt-16 pb-20 space-y-10">
    <div class="space-y-3">
        <p class="text-sm font-semibold text-cyan-200">{{ __('marketing.nav.pricing') }}</p>
        <h1 class="text-4xl font-semibold text-white">{{ __('marketing.pricing.title') }}</h1>
        <p class="text-lg text-slate-300 max-w-3xl">{{ __('marketing.pricing.description') }}</p>
    </div>
    <div class="grid gap-6 md:grid-cols-3">
        @foreach(__('marketing.pricing.plans') as $plan)
            <div class="rounded-3xl border border-white/5 bg-white/5 p-6 shadow-card space-y-4 {{ !empty($plan['featured']) ? 'ring-2 ring-cyan-300/60 bg-slate-900/70' : '' }}">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-semibold text-white">{{ $plan['name'] }}</h3>
                    @if(!empty($plan['featured']))
                        <span class="text-xs font-semibold text-cyan-200 rounded-full bg-white/10 px-3 py-1">Popular</span>
                    @endif
                </div>
                <p class="text-3xl font-semibold text-white">{{ $plan['price'] }} <span class="text-sm font-normal text-slate-400">/{{ $plan['period'] }}</span></p>
                <ul class="space-y-2 text-sm text-slate-200">
                    @foreach($plan['features'] as $item)
                        <li class="flex items-start gap-2">
                            <span class="mt-1 inline-flex h-5 w-5 items-center justify-center rounded-full bg-white/10 text-xs">✓</span>
                            <span>{{ $item }}</span>
                        </li>
                    @endforeach
                </ul>
                @include('marketing.partials.primary-button', [
                    'href' => '#contact',
                    'label' => __('marketing.nav.get_started'),
                    'size' => 'md'
                ])
            </div>
        @endforeach
    </div>
    <p class="text-sm text-slate-400">{{ __('marketing.pricing.note') }}</p>
</section>
@endsection
