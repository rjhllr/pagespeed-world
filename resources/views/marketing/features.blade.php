@extends('layouts.marketing')

@section('content')
<section class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 pt-16 pb-20 space-y-10">
    <div class="space-y-3">
        <p class="text-sm font-semibold text-cyan-200">{{ __('marketing.nav.features') }}</p>
        <h1 class="text-4xl font-semibold text-white">{{ __('marketing.features.title') }}</h1>
        <p class="text-lg text-slate-300 max-w-3xl">{{ __('marketing.features.description') }}</p>
    </div>
    <div class="grid gap-6 md:grid-cols-2">
        @foreach(__('marketing.features.sections') as $section)
            <div class="rounded-3xl border border-white/5 bg-white/5 p-6 shadow-card space-y-2">
                <h3 class="text-xl font-semibold text-white">{{ $section['title'] }}</h3>
                <p class="text-sm text-slate-300">{{ $section['body'] }}</p>
            </div>
        @endforeach
    </div>
</section>
@endsection
