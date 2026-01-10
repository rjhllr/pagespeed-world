<x-app-layout>
    <x-slot name="title">Dashboard - pagespeed.world</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Performance Dashboard</h1>
            <p class="mt-1 text-sm text-gray-600">Monitor your pages' performance scores</p>
        </div>

        @if($pages->isEmpty())
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No pages configured</h3>
            <p class="mt-1 text-sm text-gray-500">Contact your administrator to add pages for monitoring.</p>
        </div>
        @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($pages as $page)
            <a href="/dashboard/pages/{{ $page->id }}" class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex-1 min-w-0">
                            <h3 class="text-lg font-semibold text-gray-900 truncate">{{ $page->name }}</h3>
                            <p class="text-sm text-gray-500 truncate">{{ $page->url }}</p>
                        </div>
                        @if($page->is_active)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Active
                        </span>
                        @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            Inactive
                        </span>
                        @endif
                    </div>
                    
                    <div class="mt-6 grid grid-cols-2 gap-4">
                        <!-- Mobile Score -->
                        <div class="text-center">
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Mobile</p>
                            @php
                                $mobileScore = $page->latestMobileResult?->performance_score;
                                $mobileColor = $mobileScore >= 90 ? 'text-green-600' : ($mobileScore >= 50 ? 'text-orange-500' : 'text-red-500');
                            @endphp
                            <p class="mt-1 text-3xl font-bold {{ $mobileScore ? $mobileColor : 'text-gray-400' }}">
                                {{ $mobileScore ? round($mobileScore) : '—' }}
                            </p>
                        </div>
                        <!-- Desktop Score -->
                        <div class="text-center">
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Desktop</p>
                            @php
                                $desktopScore = $page->latestDesktopResult?->performance_score;
                                $desktopColor = $desktopScore >= 90 ? 'text-green-600' : ($desktopScore >= 50 ? 'text-orange-500' : 'text-red-500');
                            @endphp
                            <p class="mt-1 text-3xl font-bold {{ $desktopScore ? $desktopColor : 'text-gray-400' }}">
                                {{ $desktopScore ? round($desktopScore) : '—' }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <p class="text-xs text-gray-500">
                            Last crawled: {{ $page->last_crawled_at ? $page->last_crawled_at->diffForHumans() : 'Never' }}
                        </p>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
        @endif
    </div>
</x-app-layout>
