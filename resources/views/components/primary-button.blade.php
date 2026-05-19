<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex h-11 items-center justify-center rounded-clay bg-clay-primary px-5 text-sm font-semibold text-clay-on-primary shadow-sm transition hover:bg-clay-primary-active focus:outline-none focus:ring-2 focus:ring-clay-teal/35 focus:ring-offset-2 focus:ring-offset-clay-canvas disabled:opacity-40']) }}>
    {{ $slot }}
</button>
