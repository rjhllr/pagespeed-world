<x-app-layout>
    <x-slot name="title">{{ $page->name }} - pagespeed.world</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <a href="/dashboard" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-4">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to Dashboard
            </a>
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $page->name }}</h1>
                    <a href="{{ $page->url }}" target="_blank" class="text-sm text-blue-600 hover:underline">
                        {{ $page->url }}
                        <svg class="w-3 h-3 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                    </a>
                </div>
                <span class="text-sm text-gray-500">
                    Last crawled: {{ $page->last_crawled_at ? $page->last_crawled_at->diffForHumans() : 'Never' }}
                </span>
            </div>
        </div>

        <!-- Current Scores -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Mobile Scores -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    Mobile
                </h2>
                <div class="grid grid-cols-4 gap-4">
                    @foreach(['performance' => 'Performance', 'accessibility' => 'Accessibility', 'bestPractices' => 'Best Practices', 'seo' => 'SEO'] as $key => $label)
                    @php
                        $score = $chartData['scores']['mobile'][$key];
                        $bgColor = $score >= 90 ? 'bg-green-100' : ($score >= 50 ? 'bg-orange-100' : 'bg-red-100');
                        $textColor = $score >= 90 ? 'text-green-700' : ($score >= 50 ? 'text-orange-700' : 'text-red-700');
                    @endphp
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto rounded-full {{ $score ? $bgColor : 'bg-gray-100' }} flex items-center justify-center">
                            <span class="text-xl font-bold {{ $score ? $textColor : 'text-gray-400' }}">{{ $score ?? '—' }}</span>
                        </div>
                        <p class="mt-2 text-xs font-medium text-gray-600">{{ $label }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Desktop Scores -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    Desktop
                </h2>
                <div class="grid grid-cols-4 gap-4">
                    @foreach(['performance' => 'Performance', 'accessibility' => 'Accessibility', 'bestPractices' => 'Best Practices', 'seo' => 'SEO'] as $key => $label)
                    @php
                        $score = $chartData['scores']['desktop'][$key];
                        $bgColor = $score >= 90 ? 'bg-green-100' : ($score >= 50 ? 'bg-orange-100' : 'bg-red-100');
                        $textColor = $score >= 90 ? 'text-green-700' : ($score >= 50 ? 'text-orange-700' : 'text-red-700');
                    @endphp
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto rounded-full {{ $score ? $bgColor : 'bg-gray-100' }} flex items-center justify-center">
                            <span class="text-xl font-bold {{ $score ? $textColor : 'text-gray-400' }}">{{ $score ?? '—' }}</span>
                        </div>
                        <p class="mt-2 text-xs font-medium text-gray-600">{{ $label }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Performance History Chart -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Performance History</h2>
            <div id="performanceChart" style="height: 400px;"></div>
        </div>

        <!-- Core Web Vitals Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">LCP (Largest Contentful Paint)</h2>
                <div id="lcpChart" style="height: 300px;"></div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">FCP (First Contentful Paint)</h2>
                <div id="fcpChart" style="height: 300px;"></div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">TBT (Total Blocking Time)</h2>
                <div id="tbtChart" style="height: 300px;"></div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">CLS (Cumulative Layout Shift)</h2>
                <div id="clsChart" style="height: 300px;"></div>
            </div>
        </div>

        <!-- Bundle Size Section -->
        @if(isset($chartData['bundleSize']))
        <div class="mb-8">
            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                <svg class="w-6 h-6 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path>
                </svg>
                Bundle Size Analysis
            </h2>

            <!-- Current Bundle Size Stats -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Current Bundle Size</h3>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <div class="text-2xl font-bold text-gray-900">{{ $chartData['bundleSize']['current']['totalFormatted'] ?? '—' }}</div>
                        <p class="text-sm text-gray-600 mt-1">Total Size</p>
                    </div>
                    <div class="text-center p-4 bg-yellow-50 rounded-lg">
                        <div class="text-2xl font-bold text-yellow-700">{{ $chartData['bundleSize']['current']['javascriptFormatted'] ?? '—' }}</div>
                        <p class="text-sm text-gray-600 mt-1">JavaScript</p>
                    </div>
                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                        <div class="text-2xl font-bold text-blue-700">{{ $chartData['bundleSize']['current']['cssFormatted'] ?? '—' }}</div>
                        <p class="text-sm text-gray-600 mt-1">CSS</p>
                    </div>
                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <div class="text-2xl font-bold text-green-700">{{ $chartData['bundleSize']['current']['imagesFormatted'] ?? '—' }}</div>
                        <p class="text-sm text-gray-600 mt-1">Images</p>
                    </div>
                    <div class="text-center p-4 bg-purple-50 rounded-lg">
                        <div class="text-2xl font-bold text-purple-700">{{ $chartData['bundleSize']['current']['fontsFormatted'] ?? '—' }}</div>
                        <p class="text-sm text-gray-600 mt-1">Fonts</p>
                    </div>
                    <div class="text-center p-4 bg-indigo-50 rounded-lg">
                        <div class="text-2xl font-bold text-indigo-700">{{ $chartData['bundleSize']['current']['requests'] ?? '—' }}</div>
                        <p class="text-sm text-gray-600 mt-1">Requests</p>
                    </div>
                </div>
            </div>

            <!-- Bundle Size History Charts -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Total Bundle Size History</h3>
                    <div id="bundleTotalChart" style="height: 300px;"></div>
                </div>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Bundle Breakdown History</h3>
                    <div id="bundleBreakdownChart" style="height: 300px;"></div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <x-slot name="scripts">
    <script>
        const chartData = @json($chartData);
        
        // Performance History Chart
        const performanceChart = echarts.init(document.getElementById('performanceChart'));
        performanceChart.setOption({
            tooltip: {
                trigger: 'axis',
                axisPointer: { type: 'cross' }
            },
            legend: {
                data: ['Mobile', 'Desktop'],
                bottom: 0
            },
            grid: {
                left: '3%',
                right: '4%',
                bottom: '15%',
                containLabel: true
            },
            xAxis: {
                type: 'category',
                boundaryGap: false,
                data: chartData.performance.mobile.dates.length > chartData.performance.desktop.dates.length 
                    ? chartData.performance.mobile.dates 
                    : chartData.performance.desktop.dates,
                axisLabel: { rotate: 45 }
            },
            yAxis: {
                type: 'value',
                min: 0,
                max: 100,
                axisLabel: { formatter: '{value}' }
            },
            series: [
                {
                    name: 'Mobile',
                    type: 'line',
                    smooth: true,
                    data: chartData.performance.mobile.scores,
                    lineStyle: { color: '#3B82F6' },
                    itemStyle: { color: '#3B82F6' },
                    areaStyle: { color: 'rgba(59, 130, 246, 0.1)' },
                    markLine: {
                        silent: true,
                        data: [
                            { yAxis: 90, lineStyle: { color: '#10B981', type: 'dashed' } },
                            { yAxis: 50, lineStyle: { color: '#F59E0B', type: 'dashed' } }
                        ]
                    }
                },
                {
                    name: 'Desktop',
                    type: 'line',
                    smooth: true,
                    data: chartData.performance.desktop.scores,
                    lineStyle: { color: '#10B981' },
                    itemStyle: { color: '#10B981' },
                    areaStyle: { color: 'rgba(16, 185, 129, 0.1)' }
                }
            ]
        });

        // Helper function for metric charts
        function createMetricChart(elementId, mobileData, desktopData, dates, unit = 'ms') {
            const chart = echarts.init(document.getElementById(elementId));
            chart.setOption({
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
                    bottom: 0
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '15%',
                    containLabel: true
                },
                xAxis: {
                    type: 'category',
                    boundaryGap: false,
                    data: dates,
                    axisLabel: { rotate: 45, fontSize: 10 }
                },
                yAxis: {
                    type: 'value'
                },
                series: [
                    {
                        name: 'Mobile',
                        type: 'line',
                        smooth: true,
                        data: mobileData,
                        lineStyle: { color: '#3B82F6' },
                        itemStyle: { color: '#3B82F6' }
                    },
                    {
                        name: 'Desktop',
                        type: 'line',
                        smooth: true,
                        data: desktopData,
                        lineStyle: { color: '#10B981' },
                        itemStyle: { color: '#10B981' }
                    }
                ]
            });
            return chart;
        }

        // Create metric charts
        const mobileDates = chartData.webVitals.mobile.dates;
        const desktopDates = chartData.webVitals.desktop.dates;
        const dates = mobileDates.length > desktopDates.length ? mobileDates : desktopDates;

        createMetricChart('lcpChart', chartData.webVitals.mobile.lcp, chartData.webVitals.desktop.lcp, dates);
        createMetricChart('fcpChart', chartData.webVitals.mobile.fcp, chartData.webVitals.desktop.fcp, dates);
        createMetricChart('tbtChart', chartData.webVitals.mobile.tbt, chartData.webVitals.desktop.tbt, dates);
        createMetricChart('clsChart', chartData.webVitals.mobile.cls, chartData.webVitals.desktop.cls, dates, '');

        // Bundle Size Charts
        if (chartData.bundleSize && document.getElementById('bundleTotalChart')) {
            // Total Bundle Size Chart
            const bundleTotalChart = echarts.init(document.getElementById('bundleTotalChart'));
            bundleTotalChart.setOption({
                tooltip: {
                    trigger: 'axis',
                    formatter: function(params) {
                        let result = params[0].axisValue + '<br/>';
                        params.forEach(p => {
                            const value = p.value !== null ? (p.value >= 1024 ? (p.value / 1024).toFixed(2) + ' MB' : p.value + ' KB') : 'N/A';
                            result += p.marker + ' ' + p.seriesName + ': ' + value + '<br/>';
                        });
                        return result;
                    }
                },
                legend: {
                    data: ['Total Size'],
                    bottom: 0
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '15%',
                    containLabel: true
                },
                xAxis: {
                    type: 'category',
                    boundaryGap: false,
                    data: chartData.bundleSize.dates,
                    axisLabel: { rotate: 45, fontSize: 10 }
                },
                yAxis: {
                    type: 'value',
                    axisLabel: {
                        formatter: function(value) {
                            return value >= 1024 ? (value / 1024).toFixed(1) + ' MB' : value + ' KB';
                        }
                    }
                },
                series: [
                    {
                        name: 'Total Size',
                        type: 'line',
                        smooth: true,
                        data: chartData.bundleSize.total,
                        lineStyle: { color: '#6366F1' },
                        itemStyle: { color: '#6366F1' },
                        areaStyle: { color: 'rgba(99, 102, 241, 0.1)' }
                    }
                ]
            });

            // Bundle Breakdown Chart (stacked area)
            const bundleBreakdownChart = echarts.init(document.getElementById('bundleBreakdownChart'));
            bundleBreakdownChart.setOption({
                tooltip: {
                    trigger: 'axis',
                    formatter: function(params) {
                        let result = params[0].axisValue + '<br/>';
                        params.forEach(p => {
                            const value = p.value !== null ? (p.value >= 1024 ? (p.value / 1024).toFixed(2) + ' MB' : p.value + ' KB') : 'N/A';
                            result += p.marker + ' ' + p.seriesName + ': ' + value + '<br/>';
                        });
                        return result;
                    }
                },
                legend: {
                    data: ['JavaScript', 'CSS', 'Images', 'Fonts'],
                    bottom: 0
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '15%',
                    containLabel: true
                },
                xAxis: {
                    type: 'category',
                    boundaryGap: false,
                    data: chartData.bundleSize.dates,
                    axisLabel: { rotate: 45, fontSize: 10 }
                },
                yAxis: {
                    type: 'value',
                    axisLabel: {
                        formatter: function(value) {
                            return value >= 1024 ? (value / 1024).toFixed(1) + ' MB' : value + ' KB';
                        }
                    }
                },
                series: [
                    {
                        name: 'JavaScript',
                        type: 'line',
                        stack: 'Total',
                        smooth: true,
                        data: chartData.bundleSize.javascript,
                        lineStyle: { color: '#EAB308' },
                        itemStyle: { color: '#EAB308' },
                        areaStyle: { color: 'rgba(234, 179, 8, 0.3)' }
                    },
                    {
                        name: 'CSS',
                        type: 'line',
                        stack: 'Total',
                        smooth: true,
                        data: chartData.bundleSize.css,
                        lineStyle: { color: '#3B82F6' },
                        itemStyle: { color: '#3B82F6' },
                        areaStyle: { color: 'rgba(59, 130, 246, 0.3)' }
                    },
                    {
                        name: 'Images',
                        type: 'line',
                        stack: 'Total',
                        smooth: true,
                        data: chartData.bundleSize.images,
                        lineStyle: { color: '#22C55E' },
                        itemStyle: { color: '#22C55E' },
                        areaStyle: { color: 'rgba(34, 197, 94, 0.3)' }
                    },
                    {
                        name: 'Fonts',
                        type: 'line',
                        stack: 'Total',
                        smooth: true,
                        data: chartData.bundleSize.fonts,
                        lineStyle: { color: '#A855F7' },
                        itemStyle: { color: '#A855F7' },
                        areaStyle: { color: 'rgba(168, 85, 247, 0.3)' }
                    }
                ]
            });

            // Add bundle charts to resize handler
            window.addEventListener('resize', () => {
                bundleTotalChart.resize();
                bundleBreakdownChart.resize();
            });
        }

        // Resize charts on window resize
        window.addEventListener('resize', () => {
            performanceChart.resize();
        });
    </script>
    </x-slot>
</x-app-layout>
