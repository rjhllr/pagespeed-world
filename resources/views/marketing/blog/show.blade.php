@extends('layouts.marketing')

@section('content')
<section class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 pt-16 pb-20 space-y-6">
    <div class="space-y-2">
        <a href="{{ route('marketing.blog.index', app()->getLocale()) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-cyan-200 hover:text-white">
            ← {{ __('marketing.blog.back') }}
        </a>
        <p class="text-xs text-slate-400 uppercase tracking-wide">{{ $post['category'] }} · {{ $post['reading_time'] }}</p>
        <h1 class="text-4xl font-semibold text-white">{{ $content['title'] ?? $post['slug'] }}</h1>
    </div>
    <article class="article-content space-y-4 text-slate-200 leading-relaxed">
        {!! $content['body'] ?? '' !!}
    </article>
</section>
@endsection
