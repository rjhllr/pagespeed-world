@extends('layouts.marketing')

@section('content')
<section class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 pt-16 pb-20 space-y-8">
    <div class="space-y-3">
        <p class="text-sm font-semibold text-cyan-200">{{ __('marketing.footer.contact') }}</p>
        <h1 class="text-4xl font-semibold text-white">{{ __('marketing.contact.title') }}</h1>
        <p class="text-lg text-slate-300">{{ __('marketing.contact.description') }}</p>
    </div>

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-500/30 bg-emerald-500/10 p-4">
            <p class="text-sm text-emerald-300">{{ session('success') }}</p>
        </div>
    @endif

    <div class="rounded-3xl border border-white/5 bg-white/5 p-6 sm:p-8 shadow-card">
        <form method="POST" action="{{ route('marketing.contact.submit', app()->getLocale()) }}" class="space-y-6">
            @csrf

            <div class="grid gap-6 sm:grid-cols-2">
                <div class="space-y-2">
                    <label for="name" class="block text-sm font-medium text-slate-200">
                        {{ __('marketing.contact.form.name') }} <span class="text-red-400">*</span>
                    </label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        required
                        class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-white placeholder-slate-400 focus:border-cyan-400 focus:outline-none focus:ring-1 focus:ring-cyan-400 transition"
                        placeholder="{{ __('marketing.contact.form.name_placeholder') }}"
                    >
                    @error('name')
                        <p class="text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="email" class="block text-sm font-medium text-slate-200">
                        {{ __('marketing.contact.form.email') }} <span class="text-red-400">*</span>
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-white placeholder-slate-400 focus:border-cyan-400 focus:outline-none focus:ring-1 focus:ring-cyan-400 transition"
                        placeholder="{{ __('marketing.contact.form.email_placeholder') }}"
                    >
                    @error('email')
                        <p class="text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="space-y-2">
                <label for="subject" class="block text-sm font-medium text-slate-200">
                    {{ __('marketing.contact.form.subject') }} <span class="text-red-400">*</span>
                </label>
                <input
                    type="text"
                    id="subject"
                    name="subject"
                    value="{{ old('subject') }}"
                    required
                    class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-white placeholder-slate-400 focus:border-cyan-400 focus:outline-none focus:ring-1 focus:ring-cyan-400 transition"
                    placeholder="{{ __('marketing.contact.form.subject_placeholder') }}"
                >
                @error('subject')
                    <p class="text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-2">
                <label for="message" class="block text-sm font-medium text-slate-200">
                    {{ __('marketing.contact.form.message') }} <span class="text-red-400">*</span>
                </label>
                <textarea
                    id="message"
                    name="message"
                    rows="6"
                    required
                    class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-white placeholder-slate-400 focus:border-cyan-400 focus:outline-none focus:ring-1 focus:ring-cyan-400 transition resize-none"
                    placeholder="{{ __('marketing.contact.form.message_placeholder') }}"
                >{{ old('message') }}</textarea>
                @error('message')
                    <p class="text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            @if(config('services.turnstile.site_key'))
                <div class="space-y-2">
                    <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.site_key') }}" data-theme="dark"></div>
                    @error('cf-turnstile-response')
                        <p class="text-sm text-red-400">{{ $message }}</p>
                    @enderror
                    @error('turnstile')
                        <p class="text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            @endif

            <div class="pt-2">
                <button
                    type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-brand-500 to-cyan-400 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-brand-500/25 hover:shadow-brand-500/40 transition-all duration-200 hover:scale-[1.02] active:scale-[0.98]"
                >
                    {{ __('marketing.contact.form.submit') }}
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </button>
            </div>
        </form>
    </div>

    <div class="rounded-3xl border border-white/5 bg-white/5 p-6 sm:p-8 shadow-card">
        <h2 class="text-xl font-semibold text-white mb-4">{{ __('marketing.contact.other_ways') }}</h2>
        <div class="space-y-3 text-slate-300">
            <p>
                <span class="font-medium text-white">{{ __('marketing.contact.email_label') }}:</span>
                <a href="mailto:{{ config('services.contact.email') }}" class="text-cyan-300 hover:text-cyan-200 transition">
                    {{ config('services.contact.email') }}
                </a>
            </p>
        </div>
    </div>
</section>
@endsection

@push('scripts')
@if(config('services.turnstile.site_key'))
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
@endif
@endpush
