@php
    $latestBundle = $chartData['bundleSize']['current'] ?? null;
@endphp

<x-app-layout :title="$page->name . ' - pagespeed.world'">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
        <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.25em] text-slate-400">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 text-slate-300 hover:text-white">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back
            </a>
            <span class="h-1.5 w-1.5 rounded-full bg-brand-400"></span>
            Dashboard
        </div>

        <div class="relative overflow-hidden rounded-2xl border border-white/10 shadow-card">
            @if($heroBackgroundUrl)
                <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ $heroBackgroundUrl }}')"></div>
                <div class="absolute inset-0 bg-gradient-to-br from-slate-950/55 via-slate-950/45 to-slate-950/55"></div>
            @else
                <div class="absolute inset-0 bg-gradient-to-br from-slate-950 via-slate-950 to-slate-900"></div>
            @endif
            <div class="relative p-6 space-y-6">
                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                    <div class="space-y-2">
                        <p class="text-xs uppercase tracking-[0.18em] text-slate-300">Page</p>
                        <div class="flex items-center gap-3">
                            <h1 class="text-4xl font-semibold text-white drop-shadow">{{ $page->name }}</h1>
                            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold {{ $page->is_active ? 'bg-emerald-500/25 text-emerald-100 border border-emerald-400/50' : 'bg-white/10 text-slate-100 border border-white/20' }}">
                                <span class="h-2 w-2 rounded-full {{ $page->is_active ? 'bg-emerald-200' : 'bg-slate-300' }}"></span>
                                {{ $page->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        <a href="{{ $page->url }}" target="_blank" class="inline-flex items-center gap-2 text-sm text-cyan-100 hover:text-white">
                            {{ $page->url }}
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                        </a>
                    </div>
                    <div class="flex flex-wrap gap-2 text-xs text-slate-100">
                        <span class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-white/10 border border-white/20 backdrop-blur">
                            <span class="h-2 w-2 rounded-full bg-brand-300"></span>
                            Last crawled {{ $page->last_crawled_at ? $page->last_crawled_at->diffForHumans() : '—' }}
                        </span>
                        @if($latestBundle)
                            <span class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-white/10 border border-white/20 backdrop-blur">
                                <span class="h-2 w-2 rounded-full bg-cyan-300"></span>
                                Transfer {{ $latestBundle['totalTransferFormatted'] ?? '—' }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-white/10 border border-white/20 rounded-xl p-5 backdrop-blur">
                        <p class="text-[11px] uppercase tracking-wide text-slate-200">Mobile</p>
                        <div class="mt-1 text-4xl font-semibold text-white drop-shadow-sm">
                            {{ $chartData['scores']['mobile']['performance'] ?? '—' }}
                        </div>
                        <p class="text-xs text-slate-200 mt-1">Perf · A11y · BP · SEO</p>
                        <div class="mt-3 grid grid-cols-4 gap-1 text-[11px] text-slate-100">
                            @foreach(['performance' => 'P', 'accessibility' => 'A', 'bestPractices' => 'BP', 'seo' => 'SEO'] as $key => $label)
                                <div class="flex flex-col items-center bg-white/10 rounded-md py-2">
                                    <span class="font-semibold text-white">{{ $chartData['scores']['mobile'][$key] ?? '—' }}</span>
                                    <span class="text-[10px] text-slate-200">{{ $label }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="bg-white/10 border border-white/20 rounded-xl p-5 backdrop-blur">
                        <p class="text-[11px] uppercase tracking-wide text-slate-200">Desktop</p>
                        <div class="mt-1 text-4xl font-semibold text-white drop-shadow-sm">
                            {{ $chartData['scores']['desktop']['performance'] ?? '—' }}
                        </div>
                        <p class="text-xs text-slate-200 mt-1">Perf · A11y · BP · SEO</p>
                        <div class="mt-3 grid grid-cols-4 gap-1 text-[11px] text-slate-100">
                            @foreach(['performance' => 'P', 'accessibility' => 'A', 'bestPractices' => 'BP', 'seo' => 'SEO'] as $key => $label)
                                <div class="flex flex-col items-center bg-white/10 rounded-md py-2">
                                    <span class="font-semibold text-white">{{ $chartData['scores']['desktop'][$key] ?? '—' }}</span>
                                    <span class="text-[10px] text-slate-200">{{ $label }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="bg-white/10 border border-white/20 rounded-xl p-5 backdrop-blur flex flex-col justify-between">
                        <div>
                            <p class="text-[11px] uppercase tracking-wide text-slate-200">Bundle</p>
                            <div class="mt-1 text-2xl font-semibold text-white drop-shadow-sm">
                                {{ $latestBundle['totalTransferFormatted'] ?? '—' }} transfer
                            </div>
                            <p class="text-xs text-slate-200 mt-1">Uncompressed {{ $latestBundle['totalFormatted'] ?? '—' }}</p>
                        </div>
                        <div class="mt-4 grid grid-cols-2 gap-2 text-xs text-slate-100">
                            <div class="bg-white/10 rounded-lg p-2">
                                <p class="text-[10px] uppercase tracking-wide text-slate-200">Requests</p>
                                <p class="text-sm font-semibold text-white">{{ $latestBundle['requests'] ?? '—' }}</p>
                            </div>
                            <div class="bg-white/10 rounded-lg p-2">
                                <p class="text-[10px] uppercase tracking-wide text-slate-200">Load</p>
                                <p class="text-sm font-semibold text-white">{{ isset($latestBundle['loadTime']) ? number_format($latestBundle['loadTime'] / 1000, 2) . 's' : '—' }}</p>
                            </div>
                            <div class="bg-white/10 rounded-lg p-2">
                                <p class="text-[10px] uppercase tracking-wide text-slate-200">Compression</p>
                                <p class="text-sm font-semibold text-white">{{ $latestBundle['compressionRatio'] !== null ? $latestBundle['compressionRatio'] . '%' : '—' }}</p>
                            </div>
                            <div class="bg-white/10 rounded-lg p-2">
                                <p class="text-[10px] uppercase tracking-wide text-slate-200">Slow reqs</p>
                                <p class="text-sm font-semibold text-white">{{ $latestBundle['slowRequests'] ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6">
            <div class="bg-white/5 border border-white/10 rounded-2xl shadow-card p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-white">Performance history</h2>
                    <span class="text-xs text-slate-400">Last 30 days</span>
                </div>
                <div id="performanceChart" style="height: 360px;"></div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white/5 border border-white/10 rounded-2xl shadow-card p-6">
                    <h2 class="text-lg font-semibold text-white mb-3">LCP (Largest Contentful Paint)</h2>
                    <div id="lcpChart" style="height: 260px;"></div>
                </div>
                <div class="bg-white/5 border border-white/10 rounded-2xl shadow-card p-6">
                    <h2 class="text-lg font-semibold text-white mb-3">FCP (First Contentful Paint)</h2>
                    <div id="fcpChart" style="height: 260px;"></div>
                </div>
                <div class="bg-white/5 border border-white/10 rounded-2xl shadow-card p-6">
                    <h2 class="text-lg font-semibold text-white mb-3">TBT (Total Blocking Time)</h2>
                    <div id="tbtChart" style="height: 260px;"></div>
                </div>
                <div class="bg-white/5 border border-white/10 rounded-2xl shadow-card p-6">
                    <h2 class="text-lg font-semibold text-white mb-3">CLS (Cumulative Layout Shift)</h2>
                    <div id="clsChart" style="height: 260px;"></div>
                </div>
            </div>

            @if(isset($chartData['bundleSize']))
                <div class="bg-white/5 border border-white/10 rounded-2xl shadow-card p-6 space-y-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-white">Bundle size analysis</h2>
                            <p class="text-sm text-slate-300">Size breakdown and timing across the latest 30 days.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-sm font-semibold text-white mb-2">Bundle size (uncompressed vs transfer)</h3>
                            <div id="bundleTotalChart" style="height: 260px;"></div>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-white mb-2">Bundle breakdown</h3>
                            <div id="bundleBreakdownChart" style="height: 260px;"></div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-sm font-semibold text-white mb-2">Download time by type</h3>
                            <div id="downloadTimeChart" style="height: 240px;"></div>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-white mb-2">Load time &amp; slow requests</h3>
                            <div id="loadTimeChart" style="height: 240px;"></div>
                        </div>
                    </div>
                </div>
            @endif

            @if(!empty($filmstripFrames))
                <div class="bg-white/5 border border-white/10 rounded-2xl shadow-card p-6 space-y-4">
                    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                        <div>
                            <h2 class="text-lg font-semibold text-white">Rendering filmstrip</h2>
                            <p class="text-sm text-slate-300">Navigate frames, click to preview, and match them to core events.</p>
                        </div>
                        <div class="flex flex-wrap gap-2 text-xs text-slate-300">
                            @if($filmstripMeta)
                                <span class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-white/5 border border-white/10">
                                    <span class="h-2 w-2 rounded-full bg-brand-400"></span>
                                    Captured {{ \Carbon\Carbon::parse($filmstripMeta['capturedAt'])->diffForHumans() }}
                                </span>
                                <span class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-white/5 border border-white/10">
                                    <span class="h-2 w-2 rounded-full bg-emerald-300"></span>
                                    {{ $filmstripMeta['requestCount'] ?? '—' }} requests
                                </span>
                                <span class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-white/5 border border-white/10">
                                    <span class="h-2 w-2 rounded-full bg-cyan-300"></span>
                                    {{ $filmstripMeta['loadTime'] ? number_format($filmstripMeta['loadTime'] / 1000, 2) . 's' : '—' }} load
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                        <div class="lg:col-span-2 space-y-3">
                            <div class="flex items-center gap-3 overflow-x-auto pb-2" id="filmstripTrack">
                                @foreach($filmstripFrames as $frame)
                                    <button
                                        type="button"
                                        data-frame="{{ $frame['timestamp'] }}"
                                        data-url="{{ $frame['url'] }}"
                                        data-event="{{ $frame['event'] ?? '' }}"
                                        class="relative shrink-0 rounded-xl border border-white/10 bg-white/5 hover:border-brand-400 transition focus:outline-none"
                                        style="width: 120px"
                                    >
                                        <div class="aspect-[4/3] overflow-hidden rounded-t-xl bg-slate-900">
                                            @if(!empty($frame['url']))
                                                <img src="{{ $frame['url'] }}" alt="Filmstrip frame" class="w-full h-full object-cover">
                                            @endif
                                        </div>
                                        <div class="px-3 py-2 text-left">
                                            <p class="text-[11px] font-semibold text-white">{{ number_format(($frame['timestamp'] ?? 0) / 1000, 2) }}s</p>
                                            <p class="text-[10px] text-slate-400">{{ $frame['event'] ?? 'Frame' }}</p>
                                        </div>
                                        @if(!empty($frame['event']))
                                            <span class="absolute -top-2 -right-2 px-2 py-1 rounded-full text-[10px] font-semibold bg-brand-500 text-white border border-brand-400/50">{{ strtoupper($frame['event']) }}</span>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        </div>
                        <div class="bg-white/5 border border-white/10 rounded-xl p-4 space-y-3">
                            <p class="text-sm font-semibold text-white flex items-center gap-2">
                                <span class="h-2 w-2 rounded-full bg-brand-400"></span>
                                Selected frame
                            </p>
                            <div class="aspect-video bg-slate-900 rounded-lg overflow-hidden">
                                <img id="filmstripPreview" src="{{ $filmstripFrames[array_key_last($filmstripFrames)]['url'] ?? '' }}" alt="Filmstrip preview" class="w-full h-full object-cover">
                            </div>
                            <div class="flex items-center gap-3 text-sm text-slate-200">
                                <span id="filmstripEvent" class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/5 border border-white/10 text-xs font-semibold">
                                    {{ !empty($filmstripFrames) && $filmstripFrames[array_key_last($filmstripFrames)]['event'] ? strtoupper($filmstripFrames[array_key_last($filmstripFrames)]['event']) : 'FRAME' }}
                                </span>
                                <span id="filmstripTimestamp" class="text-xs text-slate-400">
                                    {{ !empty($filmstripFrames) ? number_format(($filmstripFrames[array_key_last($filmstripFrames)]['timestamp'] ?? 0) / 1000, 2) . 's' : '—' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <x-slot name="scripts">
    <script>
        const chartData = @json($chartData);
        const bucketed = chartData.bucketed || {};
        const textColor = '#cbd5e1';
        const axisLine = { lineStyle: { color: '#475569' } };
        const axisLabel = { color: textColor, fontSize: 11 };
        const splitLine = { lineStyle: { color: 'rgba(148, 163, 184, 0.2)' } };
        const perfLabels = bucketed.labels || [];
        const smoothLines = bucketed.smoothing === undefined ? true : bucketed.smoothing;

        // Performance History Chart (bucketed hourly, smoothed)
        const performanceChart = echarts.init(document.getElementById('performanceChart'));
        performanceChart.setOption({
            textStyle: { color: textColor },
            tooltip: {
                trigger: 'axis',
                axisPointer: { type: 'cross' }
            },
            legend: {
                data: ['Mobile', 'Desktop'],
                bottom: 0,
                textStyle: { color: textColor }
            },
            grid: { left: '3%', right: '4%', bottom: '15%', containLabel: true },
            xAxis: {
                type: 'category',
                boundaryGap: false,
                data: perfLabels,
                axisLabel: { rotate: 45, ...axisLabel },
                axisLine,
            },
            yAxis: {
                type: 'value',
                min: 0,
                max: 100,
                axisLabel: { formatter: '{value}', ...axisLabel },
                axisLine,
                splitLine,
            },
            series: [
                {
                    name: 'Mobile',
                    type: 'line',
                    smooth: smoothLines,
                    data: bucketed.mobile?.performance || [],
                    lineStyle: { color: '#60a5fa' },
                    itemStyle: { color: '#60a5fa' },
                    areaStyle: { color: 'rgba(96, 165, 250, 0.12)' },
                    markLine: {
                        silent: true,
                        data: [
                            { yAxis: 90, lineStyle: { color: '#22c55e', type: 'dashed' } },
                            { yAxis: 50, lineStyle: { color: '#f59e0b', type: 'dashed' } }
                        ]
                    }
                },
                {
                    name: 'Desktop',
                    type: 'line',
                    smooth: smoothLines,
                    data: bucketed.desktop?.performance || [],
                    lineStyle: { color: '#34d399' },
                    itemStyle: { color: '#34d399' },
                    areaStyle: { color: 'rgba(52, 211, 153, 0.12)' }
                }
            ]
        });

        // Helper function for metric charts
        function createMetricChart(elementId, mobileData, desktopData, dates, unit = 'ms') {
            const chart = echarts.init(document.getElementById(elementId));
            chart.setOption({
                textStyle: { color: textColor },
                tooltip: {
                    trigger: 'axis',
                    formatter: function(params) {
                        let result = params[0].axisValue + '<br/>';
                        params.forEach(p => {
                            result += p.marker + ' ' + p.seriesName + ': ' + (p.value !== null ? p.value + unit : 'N/A') + '<br/>';
                        });
                        return result;
                    }
                },
                legend: {
                    data: ['Mobile', 'Desktop'],
                    bottom: 0,
                    textStyle: { color: textColor }
                },
                grid: { left: '3%', right: '4%', bottom: '15%', containLabel: true },
                xAxis: {
                    type: 'category',
                    boundaryGap: false,
                    data: dates,
                    axisLabel: { rotate: 45, fontSize: 10, color: textColor },
                    axisLine,
                    splitLine,
                },
                yAxis: {
                    type: 'value',
                    axisLabel: { color: textColor },
                    axisLine,
                    splitLine,
                },
                series: [
                    {
                        name: 'Mobile',
                        type: 'line',
                        smooth: true,
                        data: mobileData,
                        lineStyle: { color: '#60a5fa' },
                        itemStyle: { color: '#60a5fa' }
                    },
                    {
                        name: 'Desktop',
                        type: 'line',
                        smooth: true,
                        data: desktopData,
                        lineStyle: { color: '#34d399' },
                        itemStyle: { color: '#34d399' }
                    }
                ]
            });
            return chart;
        }

        const mobileDates = chartData.webVitals.mobile.dates;
        const desktopDates = chartData.webVitals.desktop.dates;
        const dates = perfLabels.length ? perfLabels : (mobileDates.length > desktopDates.length ? mobileDates : desktopDates);

        createMetricChart('lcpChart', bucketed.mobile?.lcp || chartData.webVitals.mobile.lcp, bucketed.desktop?.lcp || chartData.webVitals.desktop.lcp, dates, 'ms');
        createMetricChart('fcpChart', bucketed.mobile?.fcp || chartData.webVitals.mobile.fcp, bucketed.desktop?.fcp || chartData.webVitals.desktop.fcp, dates, 'ms');
        createMetricChart('tbtChart', bucketed.mobile?.tbt || chartData.webVitals.mobile.tbt, bucketed.desktop?.tbt || chartData.webVitals.desktop.tbt, dates, 'ms');
        createMetricChart('clsChart', bucketed.mobile?.cls || chartData.webVitals.mobile.cls, bucketed.desktop?.cls || chartData.webVitals.desktop.cls, dates, '');

        // Bundle Size Charts
        if (chartData.bundleSize && document.getElementById('bundleTotalChart')) {
            const formatSize = (value) => {
                if (value === null) return 'N/A';
                return value >= 1024 ? (value / 1024).toFixed(2) + ' MB' : value + ' KB';
            };

            const formatTime = (value) => {
                if (value === null) return 'N/A';
                return value >= 1000 ? (value / 1000).toFixed(2) + 's' : value + 'ms';
            };

            const bundleTotalChart = echarts.init(document.getElementById('bundleTotalChart'));
            bundleTotalChart.setOption({
                textStyle: { color: textColor },
                tooltip: {
                    trigger: 'axis',
                    formatter: function(params) {
                        let result = params[0].axisValue + '<br/>';
                        params.forEach(p => {
                            result += p.marker + ' ' + p.seriesName + ': ' + formatSize(p.value) + '<br/>';
                        });
                        return result;
                    }
                },
                legend: {
                    data: ['Uncompressed', 'Transfer (Gzipped)'],
                    bottom: 0,
                    textStyle: { color: textColor }
                },
                grid: { left: '3%', right: '4%', bottom: '15%', containLabel: true },
                xAxis: {
                    type: 'category',
                    boundaryGap: false,
                    data: chartData.bundleSize.dates,
                    axisLabel: { rotate: 45, fontSize: 10, color: textColor },
                    axisLine,
                    splitLine,
                },
                yAxis: {
                    type: 'value',
                    axisLabel: { formatter: (v) => formatSize(v), color: textColor },
                    axisLine,
                    splitLine,
                },
                series: [
                    {
                        name: 'Uncompressed',
                        type: 'line',
                        smooth: true,
                        data: chartData.bundleSize.total,
                        lineStyle: { color: '#a78bfa' },
                        itemStyle: { color: '#a78bfa' },
                        areaStyle: { color: 'rgba(167, 139, 250, 0.14)' }
                    },
                    {
                        name: 'Transfer (Gzipped)',
                        type: 'line',
                        smooth: true,
                        data: chartData.bundleSize.totalTransfer,
                        lineStyle: { color: '#34d399', type: 'dashed' },
                        itemStyle: { color: '#34d399' },
                        areaStyle: { color: 'rgba(52, 211, 153, 0.14)' }
                    }
                ]
            });

            const bundleBreakdownChart = echarts.init(document.getElementById('bundleBreakdownChart'));
            bundleBreakdownChart.setOption({
                textStyle: { color: textColor },
                tooltip: {
                    trigger: 'axis',
                    formatter: function(params) {
                        let result = params[0].axisValue + '<br/>';
                        params.forEach(p => {
                            result += p.marker + ' ' + p.seriesName + ': ' + formatSize(p.value) + '<br/>';
                        });
                        return result;
                    }
                },
                legend: {
                    data: ['JavaScript', 'CSS', 'Images', 'Fonts', 'HTML'],
                    bottom: 0,
                    textStyle: { color: textColor }
                },
                grid: { left: '3%', right: '4%', bottom: '15%', containLabel: true },
                xAxis: {
                    type: 'category',
                    boundaryGap: false,
                    data: chartData.bundleSize.dates,
                    axisLabel: { rotate: 45, fontSize: 10, color: textColor },
                    axisLine,
                    splitLine,
                },
                yAxis: {
                    type: 'value',
                    axisLabel: { formatter: (v) => formatSize(v), color: textColor },
                    axisLine,
                    splitLine,
                },
                series: [
                    {
                        name: 'JavaScript',
                        type: 'line',
                        stack: 'Total',
                        smooth: true,
                        data: chartData.bundleSize.javascript,
                        lineStyle: { color: '#facc15' },
                        itemStyle: { color: '#facc15' },
                        areaStyle: { color: 'rgba(250, 204, 21, 0.28)' }
                    },
                    {
                        name: 'CSS',
                        type: 'line',
                        stack: 'Total',
                        smooth: true,
                        data: chartData.bundleSize.css,
                        lineStyle: { color: '#60a5fa' },
                        itemStyle: { color: '#60a5fa' },
                        areaStyle: { color: 'rgba(96, 165, 250, 0.28)' }
                    },
                    {
                        name: 'Images',
                        type: 'line',
                        stack: 'Total',
                        smooth: true,
                        data: chartData.bundleSize.images,
                        lineStyle: { color: '#22c55e' },
                        itemStyle: { color: '#22c55e' },
                        areaStyle: { color: 'rgba(34, 197, 94, 0.28)' }
                    },
                    {
                        name: 'Fonts',
                        type: 'line',
                        stack: 'Total',
                        smooth: true,
                        data: chartData.bundleSize.fonts,
                        lineStyle: { color: '#c084fc' },
                        itemStyle: { color: '#c084fc' },
                        areaStyle: { color: 'rgba(192, 132, 252, 0.28)' }
                    },
                    {
                        name: 'HTML',
                        type: 'line',
                        stack: 'Total',
                        smooth: true,
                        data: chartData.bundleSize.html,
                        lineStyle: { color: '#fb923c' },
                        itemStyle: { color: '#fb923c' },
                        areaStyle: { color: 'rgba(251, 146, 60, 0.28)' }
                    }
                ]
            });

            const downloadTimeChart = echarts.init(document.getElementById('downloadTimeChart'));
            downloadTimeChart.setOption({
                textStyle: { color: textColor },
                tooltip: {
                    trigger: 'axis',
                    formatter: function(params) {
                        let result = params[0].axisValue + '<br/>';
                        params.forEach(p => {
                            result += p.marker + ' ' + p.seriesName + ': ' + formatTime(p.value) + '<br/>';
                        });
                        return result;
                    }
                },
                legend: {
                    data: ['JavaScript', 'CSS', 'Images'],
                    bottom: 0,
                    textStyle: { color: textColor }
                },
                grid: { left: '3%', right: '4%', bottom: '15%', containLabel: true },
                xAxis: {
                    type: 'category',
                    boundaryGap: false,
                    data: chartData.bundleSize.dates,
                    axisLabel: { rotate: 45, fontSize: 10, color: textColor },
                    axisLine,
                    splitLine,
                },
                yAxis: {
                    type: 'value',
                    axisLabel: { formatter: (v) => formatTime(v), color: textColor },
                    axisLine,
                    splitLine,
                },
                series: [
                    {
                        name: 'JavaScript',
                        type: 'line',
                        smooth: true,
                        data: chartData.bundleSize.jsDownloadTime,
                        lineStyle: { color: '#facc15' },
                        itemStyle: { color: '#facc15' }
                    },
                    {
                        name: 'CSS',
                        type: 'line',
                        smooth: true,
                        data: chartData.bundleSize.cssDownloadTime,
                        lineStyle: { color: '#60a5fa' },
                        itemStyle: { color: '#60a5fa' }
                    },
                    {
                        name: 'Images',
                        type: 'line',
                        smooth: true,
                        data: chartData.bundleSize.imageDownloadTime,
                        lineStyle: { color: '#22c55e' },
                        itemStyle: { color: '#22c55e' }
                    }
                ]
            });

            const loadTimeChart = echarts.init(document.getElementById('loadTimeChart'));
            loadTimeChart.setOption({
                textStyle: { color: textColor },
                tooltip: {
                    trigger: 'axis',
                    formatter: function(params) {
                        let result = params[0].axisValue + '<br/>';
                        params.forEach(p => {
                            if (p.seriesName === 'Load Time') {
                                result += p.marker + ' ' + p.seriesName + ': ' + formatTime(p.value) + '<br/>';
                            } else {
                                result += p.marker + ' ' + p.seriesName + ': ' + (p.value ?? 0) + '<br/>';
                            }
                        });
                        return result;
                    }
                },
                legend: {
                    data: ['Load Time', 'Slow Requests'],
                    bottom: 0,
                    textStyle: { color: textColor }
                },
                grid: { left: '3%', right: '4%', bottom: '15%', containLabel: true },
                xAxis: {
                    type: 'category',
                    boundaryGap: false,
                    data: chartData.bundleSize.dates,
                    axisLabel: { rotate: 45, fontSize: 10, color: textColor },
                    axisLine,
                    splitLine,
                },
                yAxis: [
                    {
                        type: 'value',
                        name: 'Time',
                        position: 'left',
                        axisLabel: { formatter: (v) => formatTime(v), color: textColor },
                        axisLine,
                        splitLine,
                    },
                    {
                        type: 'value',
                        name: 'Count',
                        position: 'right',
                        minInterval: 1,
                        axisLabel: { color: textColor },
                        axisLine,
                        splitLine,
                    }
                ],
                series: [
                    {
                        name: 'Load Time',
                        type: 'line',
                        smooth: true,
                        data: chartData.bundleSize.loadTime,
                        lineStyle: { color: '#06b6d4' },
                        itemStyle: { color: '#06b6d4' },
                        areaStyle: { color: 'rgba(6, 182, 212, 0.14)' }
                    },
                    {
                        name: 'Slow Requests',
                        type: 'bar',
                        yAxisIndex: 1,
                        data: chartData.bundleSize.slowRequests,
                        itemStyle: { 
                            color: function(params) {
                                return params.value > 0 ? '#f87171' : '#cbd5e1';
                            }
                        }
                    }
                ]
            });

            window.addEventListener('resize', () => {
                bundleTotalChart.resize();
                bundleBreakdownChart.resize();
                downloadTimeChart.resize();
                loadTimeChart.resize();
            });
        }

        window.addEventListener('resize', () => {
            performanceChart.resize();
        });

        // Filmstrip interactions
        const frames = @json($filmstripFrames ?? []);
        const preview = document.getElementById('filmstripPreview');
        const eventLabel = document.getElementById('filmstripEvent');
        const timestampLabel = document.getElementById('filmstripTimestamp');

        const selectFrame = (frame) => {
            if (!frame || !preview || !eventLabel || !timestampLabel) return;
            preview.src = frame.url || '';
            eventLabel.textContent = (frame.event || 'FRAME').toUpperCase();
            timestampLabel.textContent = `${(frame.timestamp ?? 0) / 1000}s`;
            document.querySelectorAll('[data-frame]').forEach(el => {
                const isActive = Number(el.dataset.frame) === Number(frame.timestamp);
                el.classList.toggle('ring-2', isActive);
                el.classList.toggle('ring-brand-400', isActive);
                el.classList.toggle('border-brand-400/60', isActive);
            });
        };

        if (frames.length && preview) {
            const buttons = document.querySelectorAll('[data-frame]');
            buttons.forEach(btn => {
                btn.addEventListener('click', () => {
                    const frame = frames.find(f => Number(f.timestamp) === Number(btn.dataset.frame));
                    selectFrame(frame);
                });
            });
            selectFrame(frames[frames.length - 1]);
        }
    </script>
    </x-slot>
</x-app-layout>
