@props(['value' => null])

<label {{ $attributes->merge(['class' => 'block text-sm font-semibold text-clay-body-strong']) }}>
    {{ $value ?? $slot }}
</label>
