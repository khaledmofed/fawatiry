<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex h-11 items-center justify-center rounded-clay border border-clay-hairline bg-white px-5 text-sm font-semibold text-clay-ink shadow-sm transition hover:bg-clay-surface-soft focus:outline-none focus:ring-2 focus:ring-clay-teal/25 focus:ring-offset-2 focus:ring-offset-clay-canvas disabled:opacity-40']) }}>
    {{ $slot }}
</button>
