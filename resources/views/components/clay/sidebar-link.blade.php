@props([
    'href',
    'active' => false,
    'icon' => null,
])

@php
    $activeClasses = 'bg-white/15 text-white shadow-inner border border-white/10';
    $idleClasses = 'text-clay-on-dark-soft hover:bg-white/10 hover:text-white border border-transparent';
@endphp

<a
    href="{{ $href }}"
    {{ $attributes->merge([
        'class' => 'group flex items-center gap-3 rounded-clay px-3 py-2.5 text-sm font-medium tracking-tight transition '.($active ? $activeClasses : $idleClasses),
    ]) }}
>
    @if ($icon)
        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white/5 text-lg leading-none group-hover:bg-white/10" aria-hidden="true">{{ $icon }}</span>
    @endif
    <span>{{ $slot }}</span>
</a>
