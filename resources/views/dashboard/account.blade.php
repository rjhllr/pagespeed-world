<x-app-layout title="Account - pagespeed.world">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-6 mb-10">
            <div>
                <p class="text-xs font-semibold tracking-[0.2em] uppercase text-slate-400">Account</p>
                <h1 class="mt-2 text-3xl font-semibold text-white">Account &amp; organization</h1>
                <p class="mt-2 text-sm text-slate-300">Keep your profile aligned with the rest of the workspace.</p>
            </div>
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white/10 text-white text-sm font-semibold border border-white/10 hover:border-white/30 transition">
                <span class="h-2 w-2 rounded-full bg-brand-400"></span>
                Back to pages
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white/5 border border-white/10 rounded-2xl shadow-card p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Profile</p>
                        <h2 class="text-lg font-semibold text-white">Your details</h2>
                    </div>
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-brand-500/20 text-brand-100 border border-brand-500/40">
                        <span class="h-2 w-2 rounded-full bg-brand-300"></span>
                        {{ __('Active') }}
                    </span>
                </div>
                <dl class="space-y-3 text-sm text-slate-200">
                    <div class="flex items-center justify-between">
                        <dt class="text-slate-400">Name</dt>
                        <dd class="font-semibold text-white">{{ $user->name }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-slate-400">Email</dt>
                        <dd class="font-semibold text-white">{{ $user->email }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-slate-400">Role</dt>
                        <dd class="font-semibold text-white">{{ $user->is_admin ? 'Admin' : 'Member' }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-slate-400">Joined</dt>
                        <dd class="font-semibold text-white">{{ $user->created_at?->format('M d, Y') }}</dd>
                    </div>
                </dl>
            </div>

            <div class="bg-white/5 border border-white/10 rounded-2xl shadow-card p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Organization</p>
                        <h2 class="text-lg font-semibold text-white">Organizations</h2>
                    </div>
                </div>
                @if($organizations->isEmpty())
                    <p class="text-sm text-slate-300">You are not assigned to any organizations yet.</p>
                @else
                    <div class="space-y-4">
                        @foreach($organizations as $organization)
                            <div class="flex items-start justify-between gap-4 p-4 rounded-xl bg-white/5 border border-white/10">
                                <div class="space-y-1">
                                    <p class="text-xs uppercase tracking-[0.14em] text-slate-400">Organization</p>
                                    <h3 class="text-lg font-semibold text-white">{{ $organization->name }}</h3>
                                    <p class="text-xs text-slate-400">{{ $organization->slug }}</p>
                                    @if($organization->description)
                                        <p class="text-sm text-slate-300">{{ $organization->description }}</p>
                                    @endif
                                </div>
                                <div class="flex flex-col items-end gap-2 text-xs text-slate-200">
                                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full font-semibold {{ $organization->is_active ? 'bg-emerald-500/15 text-emerald-100 border border-emerald-500/30' : 'bg-white/5 text-slate-200 border border-white/10' }}">
                                        <span class="h-2 w-2 rounded-full {{ $organization->is_active ? 'bg-emerald-300' : 'bg-slate-400' }}"></span>
                                        {{ $organization->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                    <div class="flex gap-2">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded bg-white/5 border border-white/10">
                                            <span class="h-1.5 w-1.5 rounded-full bg-cyan-300"></span>
                                            {{ $organization->users_count ?? 0 }} members
                                        </span>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded bg-white/5 border border-white/10">
                                            <span class="h-1.5 w-1.5 rounded-full bg-indigo-300"></span>
                                            {{ $organization->pages_count ?? 0 }} pages
                                        </span>
                                    </div>
                                    <p class="text-[11px] text-slate-400">Updated {{ $organization->updated_at?->diffForHumans() ?? '—' }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
