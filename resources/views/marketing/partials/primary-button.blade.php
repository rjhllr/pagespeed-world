@php
    $sizes = [
        'sm' => 'px-4 py-2 text-sm',
        'md' => 'px-4 py-2.5 text-sm',
        'lg' => 'px-5 py-3 text-base',
    ];
    $sizeClass = $sizes[$size ?? 'md'] ?? $sizes['md'];
@endphp

<a href="{{ $href }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-brand-500 to-cyan-400 text-white font-semibold shadow-card hover:translate-y-[-1px] transition {{ $sizeClass }}">
    {{ $label }}
    @if(!empty($icon))
        {!! $icon !!}
    @endif
</a>
