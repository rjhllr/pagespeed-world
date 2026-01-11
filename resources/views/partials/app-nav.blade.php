@php
    $mainNav = [
        [
            'label' => 'Dashboard',
            'href' => route('dashboard'),
            'active' => request()->routeIs('dashboard'),
        ],
        [
            'label' => 'Pages',
            'href' => route('dashboard'),
            'active' => request()->routeIs('dashboard') || request()->routeIs('dashboard.page'),
        ],
    ];

    $accountNav = $subnav ?: [
        [
            'label' => 'Overview',
            'href' => route('dashboard'),
            'active' => request()->routeIs('dashboard'),
        ],
        [
            'label' => 'Pages',
            'href' => route('dashboard'),
            'active' => request()->routeIs('dashboard') || request()->routeIs('dashboard.page'),
        ],
        [
            'label' => 'Account',
            'href' => route('dashboard.account'),
            'active' => request()->routeIs('dashboard.account'),
        ],
    ];
@endphp

<header class="sticky top-0 z-30 backdrop-blur bg-slate-950/70 border-b border-white/5">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between gap-6">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 font-semibold text-white">
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-brand-500 to-cyan-400 text-white shadow-card">ψ</span>
                <span class="text-lg">{{ config('app.name', 'pagespeed.world') }}</span>
            </a>

            <nav class="hidden md:flex items-center gap-6 text-sm font-medium text-slate-200">
                @foreach($mainNav as $item)
                    <a href="{{ $item['href'] }}" class="px-2 py-1 rounded-lg transition {{ $item['active'] ? 'text-white bg-white/10 shadow-sm' : 'hover:text-white' }}">
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>

            <div class="flex items-center gap-3">
                @if(auth()->user()->is_admin ?? false)
                    <a href="/admin" class="hidden sm:inline-flex items-center text-sm font-semibold text-slate-200 hover:text-white">
                        Admin
                    </a>
                @endif
                <div class="hidden sm:flex flex-col leading-tight text-right">
                    @php
                        $orgNames = auth()->user()?->organizations->pluck('name')->take(2)->join(', ');
                    @endphp
                    <span class="text-sm font-semibold text-white">{{ auth()->user()->name }}</span>
                    <span class="text-xs text-slate-300">{{ $orgNames ?: 'Account' }}</span>
                </div>
                <form method="POST" action="/logout" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center text-xs font-semibold px-3 py-2 rounded-lg border border-white/10 text-slate-200 hover:text-white hover:border-white/30 transition">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="border-t border-white/5 bg-slate-950/60">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <nav class="flex items-center gap-2 overflow-x-auto py-3 text-sm font-medium text-slate-200">
                @foreach($accountNav as $item)
                    <a href="{{ $item['href'] }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg transition {{ $item['active'] ? 'bg-white/10 text-white shadow-card' : 'hover:bg-white/5 hover:text-white' }}">
                        <span class="h-2 w-2 rounded-full {{ $item['active'] ? 'bg-brand-400' : 'bg-white/30' }}"></span>
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>
        </div>
    </div>
</header>
