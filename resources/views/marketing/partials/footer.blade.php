<footer class="border-t border-white/5 bg-slate-950/80">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 py-10 grid gap-8 md:grid-cols-4 text-sm text-slate-300">
        <div class="space-y-3">
            <div class="flex items-center gap-2 font-semibold text-white">
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-brand-500 to-cyan-400 text-white shadow-card">ψ</span>
                <span>{{ config('app.name', 'pagespeed.world') }}</span>
            </div>
            <p class="text-slate-400">{{ __('marketing.footer.tagline') }}</p>
            <div class="flex gap-3 text-slate-400">
                <a class="hover:text-white" href="mailto:hello@pagespeed.world">hello@pagespeed.world</a>
            </div>
        </div>
        <div>
            <h3 class="text-white font-semibold mb-3">{{ __('marketing.footer.product') }}</h3>
            <ul class="space-y-2">
                <li><a href="#features" class="hover:text-white">{{ __('marketing.nav.features') }}</a></li>
                <li><a href="#pricing" class="hover:text-white">{{ __('marketing.nav.pricing') }}</a></li>
                <li><a href="#faq" class="hover:text-white">{{ __('marketing.nav.faq') }}</a></li>
            </ul>
        </div>
        <div>
            <h3 class="text-white font-semibold mb-3">{{ __('marketing.footer.resources') }}</h3>
            <ul class="space-y-2">
                <li><a href="{{ route('marketing.blog.index', app()->getLocale()) }}" class="hover:text-white">{{ __('marketing.nav.blog') }}</a></li>
                <li><a href="#contact" class="hover:text-white">{{ __('marketing.footer.contact') }}</a></li>
            </ul>
        </div>
        <div>
            <h3 class="text-white font-semibold mb-3">{{ __('marketing.footer.legal') }}</h3>
            <ul class="space-y-2">
                <li><a href="#" class="hover:text-white">{{ __('marketing.footer.imprint') }}</a></li>
                <li><a href="#" class="hover:text-white">{{ __('marketing.footer.privacy') }}</a></li>
            </ul>
        </div>
    </div>
    <div class="border-t border-white/5 py-4 text-center text-xs text-slate-500">
        © {{ date('Y') }} {{ config('app.name', 'pagespeed.world') }} · {{ __('marketing.footer.rights') }}
    </div>
</footer>
