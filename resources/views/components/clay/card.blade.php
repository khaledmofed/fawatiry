@props([
    'glass' => true,
])

<div {{ $attributes->merge([
    'class' => ($glass ? 'clay-glass-panel' : 'rounded-clay-lg border border-clay-hairline bg-white shadow-clay-soft') . ' rounded-clay-lg p-6 sm:p-8',
]) }}>
    {{ $slot }}
</div>
