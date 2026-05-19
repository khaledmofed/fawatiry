@props([
    'accent' => 'neutral',
])

@php
    $map = [
        'neutral' => 'bg-clay-surface-card text-clay-ink border-clay-hairline/80',
        'success' => 'bg-emerald-50 text-emerald-800 border-emerald-200/80',
        'warning' => 'bg-amber-50 text-amber-900 border-amber-200/80',
        'danger' => 'bg-red-50 text-red-800 border-red-200/80',
        'pink' => 'bg-clay-pink/15 text-clay-ink border-clay-pink/30',
    ];
    $c = $map[$accent] ?? $map['neutral'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold tracking-wide $c"]) }}>
    {{ $slot }}
</span>
