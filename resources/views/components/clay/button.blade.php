@props([
    'variant' => 'primary',
    'type' => 'button',
])

@php
    $base = 'inline-flex h-11 shrink-0 items-center justify-center gap-2 rounded-clay px-5 text-sm font-semibold tracking-tight transition focus:outline-none focus:ring-2 focus:ring-clay-teal/30 focus:ring-offset-2 focus:ring-offset-clay-canvas disabled:pointer-events-none disabled:opacity-45';
    $styles = match ($variant) {
        'primary' => 'bg-clay-primary text-clay-on-primary hover:bg-clay-primary-active',
        'secondary' => 'border border-clay-hairline bg-clay-canvas text-clay-ink hover:bg-clay-surface-soft',
        'ghost' => 'border border-transparent bg-transparent text-clay-ink hover:bg-clay-surface-soft/80',
        'danger' => 'bg-clay-error text-white hover:bg-red-600',
        'on-color' => 'bg-white text-clay-ink hover:bg-clay-hairline-soft',
        default => 'bg-clay-primary text-clay-on-primary hover:bg-clay-primary-active',
    };
@endphp

@if ($attributes->has('href'))
    <a {{ $attributes->merge(['class' => $base.' '.$styles]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $base.' '.$styles]) }}>
        {{ $slot }}
    </button>
@endif
