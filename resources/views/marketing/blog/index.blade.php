@extends('layouts.marketing')

@section('content')
<section class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 pt-16 pb-20 space-y-10">
    <div class="space-y-3">
        <p class="text-sm font-semibold text-cyan-200">{{ __('marketing.nav.blog') }}</p>
        <h1 class="text-4xl font-semibold text-white">{{ __('marketing.blog.title') }}</h1>
        <p class="text-lg text-slate-300 max-w-3xl">{{ __('marketing.blog.description') }}</p>
    </div>
    <div class="grid gap-6 md:grid-cols-2">
        @foreach($posts as $post)
            <article class="rounded-3xl border border-white/5 bg-white/5 p-6 shadow-card space-y-3">
                <div class="flex items-center justify-between text-xs text-slate-400">
                    <span>{{ $post['category'] }}</span>
                    <span>{{ $post['reading_time'] }}</span>
                </div>
                <h2 class="text-xl font-semibold text-white">{{ $post['title'] }}</h2>
                <p class="text-sm text-slate-300">{{ $post['excerpt'] }}</p>
                <a href="{{ route('marketing.blog.show', [app()->getLocale(), $post['slug']]) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-cyan-200 hover:text-white">
                    {{ __('marketing.blog.read_more') }} →
                </a>
            </article>
        @endforeach
    </div>
</section>
@endsection
