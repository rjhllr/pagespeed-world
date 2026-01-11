@php
    $filmstrip = $getRecord()?->getFilmstripUrls() ?? [];
@endphp

@if(count($filmstrip) > 0)
<div class="filmstrip-container">
    <div class="filmstrip-scroll overflow-x-auto pb-4">
        <div class="flex gap-4 min-w-max">
            @foreach($filmstrip as $frame)
                <div class="filmstrip-frame flex flex-col items-center">
                    <a href="{{ $frame['url'] }}" target="_blank" class="block">
                        <img 
                            src="{{ $frame['url'] }}" 
                            alt="Screenshot at {{ $frame['timestamp'] }}ms"
                            class="h-40 w-auto rounded-lg shadow-md hover:shadow-lg transition-shadow border border-gray-200 dark:border-gray-700"
                            loading="lazy"
                        />
                    </a>
                    <div class="mt-2 text-center">
                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ number_format($frame['timestamp'] / 1000, 2) }}s
                        </span>
                        @if($frame['event'])
                            <span class="ml-1 inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                                @if($frame['event'] === 'start') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                @elseif($frame['event'] === 'domContentLoaded') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                @elseif($frame['event'] === 'load') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                @elseif($frame['event'] === 'networkIdle') bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200
                                @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200
                                @endif
                            ">
                                @if($frame['event'] === 'domContentLoaded')
                                    DCL
                                @elseif($frame['event'] === 'networkIdle')
                                    Idle
                                @else
                                    {{ ucfirst($frame['event']) }}
                                @endif
                            </span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    
    <div class="mt-3 flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
        <span class="flex items-center gap-1">
            <span class="inline-block w-2 h-2 rounded-full bg-blue-500"></span>
            Start
        </span>
        <span class="flex items-center gap-1">
            <span class="inline-block w-2 h-2 rounded-full bg-yellow-500"></span>
            DOM Content Loaded
        </span>
        <span class="flex items-center gap-1">
            <span class="inline-block w-2 h-2 rounded-full bg-green-500"></span>
            Load
        </span>
        <span class="flex items-center gap-1">
            <span class="inline-block w-2 h-2 rounded-full bg-purple-500"></span>
            Network Idle
        </span>
    </div>
</div>
@else
<div class="text-gray-500 dark:text-gray-400 text-sm py-4">
    No filmstrip screenshots available for this analysis.
</div>
@endif
