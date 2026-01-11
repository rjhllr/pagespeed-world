@extends('layouts.marketing')

@section('content')
<section class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 pt-16 pb-20 space-y-8">
    <div class="space-y-3">
        <p class="text-sm font-semibold text-cyan-200">{{ __('marketing.about.title') }}</p>
        <h1 class="text-4xl font-semibold text-white">{{ __('marketing.about.description') }}</h1>
        <p class="text-lg text-slate-300 max-w-3xl">{{ __('marketing.about.mission') }}</p>
    </div>
    <div class="grid gap-4 md:grid-cols-3">
        @foreach(__('marketing.about.values') as $value)
            <div class="rounded-2xl border border-white/5 bg-white/5 p-5">
                <p class="text-sm font-semibold text-white">{{ $value }}</p>
            </div>
        @endforeach
    </div>
</section>
@endsection
