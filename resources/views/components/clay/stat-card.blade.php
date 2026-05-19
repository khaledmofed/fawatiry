@props([
    'accent' => 'cream',
    'title',
    'value',
    'hint' => null,
])

@php
    $accents = [
        'pink' => 'from-clay-pink/90 to-clay-pink text-white',
        'teal' => 'from-clay-teal to-clay-teal text-clay-on-dark',
        'lavender' => 'from-clay-lavender to-clay-lavender text-clay-ink',
        'peach' => 'from-clay-peach to-clay-peach text-clay-ink',
        'ochre' => 'from-clay-ochre to-clay-ochre text-clay-ink',
        'cream' => 'from-clay-surface-card to-clay-surface-soft text-clay-ink border border-clay-hairline/60',
    ];
    $grad = $accents[$accent] ?? $accents['cream'];
@endphp

<div {{ $attributes->merge(['class' => "relative overflow-hidden rounded-clay-xl bg-gradient-to-br p-6 shadow-clay-soft $grad"]) }}>
    <p class="text-xs font-semibold uppercase tracking-widest opacity-90">{{ $title }}</p>
    <p class="mt-2 text-3xl font-medium tracking-tight">{{ $value }}</p>
    @if ($hint)
        <p class="mt-1 text-sm opacity-80">{{ $hint }}</p>
    @endif
</div>
