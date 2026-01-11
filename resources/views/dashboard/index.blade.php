<x-app-layout title="Dashboard - pagespeed.world">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="flex flex-col gap-4 mb-8">
            <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.25em] text-slate-400">
                <span class="h-1.5 w-1.5 rounded-full bg-brand-400"></span>
                Pages
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-semibold text-white">All monitored pages</h1>
                    <p class="mt-2 text-sm text-slate-300">See every page you can access with the freshest performance and weight metrics.</p>
                </div>
                <div class="flex flex-wrap gap-3 text-sm">
                    <span class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-slate-100">
                        <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                        {{ $pages->count() }} pages
                    </span>
                    <span class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-slate-100">
                        <span class="h-2 w-2 rounded-full bg-cyan-300"></span>
                        {{ now()->format('M d, Y') }}
                    </span>
                </div>
            </div>
        </div>

        @if($pages->isEmpty())
            <div class="bg-white/5 border border-white/10 rounded-2xl shadow-card p-10 text-center">
                <svg class="mx-auto h-12 w-12 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="mt-4 text-lg font-semibold text-white">No pages configured</h3>
                <p class="mt-2 text-sm text-slate-300">Contact your administrator to add pages for monitoring.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($pages as $page)
                    @php
                        $mobile = $page->latestMobileResult;
                        $desktop = $page->latestDesktopResult;
                        $latestResult = collect([$mobile, $desktop])->filter()->sortByDesc('created_at')->first();
                        $score = $latestResult?->performance_score;
                        $lcp = $latestResult?->largest_contentful_paint;
                        $fcp = $latestResult?->first_contentful_paint;
                        $tbt = $latestResult?->total_blocking_time;
                        $cls = $latestResult?->cumulative_layout_shift;
                        $bundle = $page->latestBundleSize;
                        $scoreColor = $score >= 90 ? 'text-emerald-300' : ($score >= 50 ? 'text-amber-300' : 'text-rose-300');
                    @endphp
                    <a href="{{ route('dashboard.page', $page) }}" class="block bg-white/5 border border-white/10 rounded-2xl shadow-card hover:border-white/20 transition group">
                        <div class="p-6 flex flex-col gap-5">
                            <div class="flex items-start justify-between gap-4">
                                <div class="min-w-0">
                                    <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Page</p>
                                    <h3 class="text-xl font-semibold text-white truncate">{{ $page->name }}</h3>
                                    <p class="text-sm text-slate-300 truncate">{{ $page->url }}</p>
                                </div>
                                <div class="flex flex-col items-end gap-2">
                                    <span class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full text-xs font-semibold {{ $page->is_active ? 'bg-emerald-500/15 text-emerald-100 border border-emerald-500/30' : 'bg-white/5 text-slate-200 border border-white/10' }}">
                                        <span class="h-2 w-2 rounded-full {{ $page->is_active ? 'bg-emerald-300' : 'bg-slate-400' }}"></span>
                                        {{ $page->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                    <p class="text-[11px] text-slate-400">Last crawled {{ $page->last_crawled_at ? $page->last_crawled_at->diffForHumans() : '—' }}</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-white/5 border border-white/10 rounded-xl p-4">
                                    <p class="text-xs text-slate-400 uppercase tracking-wide">Latest score</p>
                                    <div class="mt-1 text-3xl font-bold {{ $score ? $scoreColor : 'text-slate-500' }}">
                                        {{ $score ? round($score) : '—' }}
                                    </div>
                                    <div class="mt-2 flex items-center gap-2 text-xs text-slate-300">
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded bg-white/5 border border-white/10">
                                            <span class="h-1.5 w-1.5 rounded-full bg-cyan-300"></span>
                                            Mobile {{ $mobile?->performance_score ? round($mobile->performance_score) : '—' }}
                                        </span>
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded bg-white/5 border border-white/10">
                                            <span class="h-1.5 w-1.5 rounded-full bg-indigo-300"></span>
                                            Desktop {{ $desktop?->performance_score ? round($desktop->performance_score) : '—' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="bg-white/5 border border-white/10 rounded-xl p-4">
                                    <p class="text-xs text-slate-400 uppercase tracking-wide">Page weight</p>
                                    <div class="mt-1 text-lg font-semibold text-white">
                                        {{ $bundle?->total_transfer_size ? \App\Models\BundleSize::formatBytes($bundle->total_transfer_size) : '—' }}
                                    </div>
                                    <p class="text-[11px] text-slate-400 mt-1">Transfer · {{ $bundle?->total_size ? \App\Models\BundleSize::formatBytes($bundle->total_size) : 'unknown' }} uncompressed</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm">
                                <div class="bg-white/5 border border-white/10 rounded-lg p-3">
                                    <p class="text-[11px] uppercase tracking-wide text-slate-400">LCP</p>
                                    <p class="text-lg font-semibold text-white">{{ $lcp ? round($lcp) . ' ms' : '—' }}</p>
                                </div>
                                <div class="bg-white/5 border border-white/10 rounded-lg p-3">
                                    <p class="text-[11px] uppercase tracking-wide text-slate-400">FCP</p>
                                    <p class="text-lg font-semibold text-white">{{ $fcp ? round($fcp) . ' ms' : '—' }}</p>
                                </div>
                                <div class="bg-white/5 border border-white/10 rounded-lg p-3">
                                    <p class="text-[11px] uppercase tracking-wide text-slate-400">TBT</p>
                                    <p class="text-lg font-semibold text-white">{{ $tbt ? round($tbt) . ' ms' : '—' }}</p>
                                </div>
                                <div class="bg-white/5 border border-white/10 rounded-lg p-3">
                                    <p class="text-[11px] uppercase tracking-wide text-slate-400">CLS</p>
                                    <p class="text-lg font-semibold text-white">{{ $cls ? number_format($cls, 3) : '—' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="px-6 py-3 border-t border-white/5 text-xs text-slate-300 flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-brand-400"></span>
                            View dashboard with charts, bundle history &amp; filmstrip
                            <svg class="w-4 h-4 text-slate-300 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
